<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Seeder\Controllers;

use Database\Seeders\ClientSeeder;
use Database\Seeders\ExpenseSeeder;
use Database\Seeders\InvoiceSeeder;
use Database\Seeders\QuoteSeeder;
use Database\Seeders\TaskSeeder;
use FI\Http\Controllers\Controller;
use FI\Modules\Seeder\Requests\DataSeederRequest;
use FI\Support\Frequency;

class SeederController extends Controller
{
    public function index()
    {
        return view('seeder.data-seeder')->with('modules',
            [
                'client'           => trans('fi.clients'),
                'quote'            => trans('fi.quotes'),
                'invoice'          => trans('fi.invoices'),
                'recurringInvoice' => trans('fi.recurring_invoices'),
                'expense'          => trans('fi.expenses'),
                'task'             => trans('fi.tasks')
            ]
        );
    }

    public function dataSeederModal(DataSeederRequest $request)
    {
        $modalName  = $seederName = ucwords(request('module'));
        $modalName  = $modalName == 'Task' ? 'TaskList' : $modalName . 's';
        $modal      = 'FI\Modules\\' . $modalName . '\\Models\\' . $seederName;
        $modalClass = 'FI\Modules\Seeder\Seeds\\' . $seederName . 'Seeder';

        $seeder = new $modalClass(request('number_of_seed_data'));
        $seeds  = $seeder->run();

        $tableHeader = $modal::getheaders();
        return view('seeder._' . request('module') . '_table')
            ->with(['seeds' => $seeds, 'tableHeaders' => $tableHeader])
            ->with('frequencies', Frequency::lists())
            ->with('typeLabels', ['lead' => 'badge-warning', 'prospect' => 'badge-danger', 'customer' => 'badge-success', 'affiliate' => 'badge-info', 'other' => 'badge-secondary'])
            ->render();
    }

}