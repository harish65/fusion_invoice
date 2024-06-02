<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Addons\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Addons\Models\Addon;
use FI\Support\Directory;
use FI\Support\Migrations;

class AddonController extends Controller
{
    private $migrations;

    public function __construct(Migrations $migrations)
    {
        $this->migrations = $migrations;
    }

    public function index()
    {
        if (!config('app.demo'))
        {
            $this->refreshList();

            return view('addons.index')
                ->with('addons', Addon::orderBy('name')->get());
        }
        else
        {
            return redirect()->route('dashboard.index')->withErrors(trans('fi.functionality_not_available_on_demo'));
        }
    }

    public function install($id)
    {
        $addon = Addon::find($id);

        try
        {
            $migrator = app('migrator');
            $migrator->run(addon_path($addon->path . '/Migrations'));
        }
        catch (\Exception $e)
        {
            return redirect()->route('addons.index')->withErrors(trans('fi.addon_install_error'));
        }

        $addon->enabled = 1;

        $addon->save();

        return redirect()->route('addons.index')->with('alertSuccess', trans('fi.addon_installed_success'));
    }

    public function upgrade($id)
    {
        $addon = Addon::find($id);

        $this->migrations->runMigrations(addon_path($addon->path . '/Migrations'));

        return redirect()->route('addons.index');
    }

    public function uninstall($id)
    {
        Addon::destroy($id);

        return redirect()->route('addons.index');
    }

    private function refreshList()
    {
        $addons = Directory::listDirectories(addon_path());

        foreach ($addons as $addon)
        {
            $setupClass = 'Addons\\' . $addon . '\Setup';

            $setupClass = new $setupClass;

            $addonRecord = $setupClass->properties;

            if (!Addon::where('name', $addonRecord['name'])->count())
            {
                $addonRecord['path'] = $addon;

                Addon::create($addonRecord);
            }
        }
    }

    public function disableModal()
    {
        try
        {
            return view('addons._disable_addons_modal')->with('url', request('action'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}