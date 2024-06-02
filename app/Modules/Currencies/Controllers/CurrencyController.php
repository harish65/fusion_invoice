<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Currencies\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Currencies\Models\Currency;
use FI\Modules\Currencies\Requests\CurrencyStoreRequest;
use FI\Modules\Currencies\Requests\CurrencyUpdateRequest;
use FI\Modules\Currencies\Support\CurrencyConverterFactory;
use FI\Modules\Settings\Models\Setting;
use FI\Traits\ReturnUrl;

class CurrencyController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $currencies = Currency::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage'));

        return view('currencies.index')
            ->with('currencies', $currencies)
            ->with('baseCurrency', config('fi.baseCurrency'));
    }

    public function create()
    {
        return view('currencies.form')
            ->with('separators', Currency::getSeparators())
            ->with('editMode', false);
    }

    public function store(CurrencyStoreRequest $request)
    {
        $input              = $request->except(['setting']);
        $input['decimal']   = html_entity_decode($input['decimal']);
        $input['thousands'] = $input['thousands'] == '&#39;' ? "'" : html_entity_decode($input['thousands']);
        $settings           = $request->input('setting', []);
        Currency::create($input);

        foreach ($settings as $key => $value)
        {
            Setting::saveByKey($key, $input['code']);
        }

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        return view('currencies.form')
            ->with('editMode', true)
            ->with('separators', Currency::getSeparators())
            ->with('currency', Currency::find($id));
    }

    public function update(CurrencyUpdateRequest $request, $id)
    {
        $input    = $request->except(['setting']);
        $settings = $request->input('setting', []);

        $currency = Currency::find($id);

        $currency->fill($input);

        $currency->decimal   = html_entity_decode($request->get('decimal'));
        $currency->thousands = $request->get('thousands') == '&#39;' ? "'" : html_entity_decode($request->get('thousands'));

        $currency->save();

        foreach ($settings as $key => $value)
        {
            Setting::saveByKey($key, $currency->code);
        }

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $currency = Currency::find($id);

        if ($currency->in_use)
        {
            return response()->json(['success' => false, 'message' => trans('fi.cannot_delete_record_in_use')], 400);
        }
        else
        {
            Currency::destroy($id);
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);
        }
    }

    public function getExchangeRate()
    {
        $currencyConverter = CurrencyConverterFactory::create();

        return $currencyConverter->convert(config('fi.baseCurrency'), request('currency_code'));
    }

    public function deleteModal()
    {
        try
        {
            return view('layouts._delete_modal')->with('url', request('action'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}