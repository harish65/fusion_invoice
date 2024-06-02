<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_taxes_default_rate1') !!}">
                {{ trans('fi.default_item_tax_rate') }}: </label>
            {!! Form::select('setting[itemTaxRate]', $taxRates, config('fi.itemTaxRate'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_taxes_default_rate2') !!}">
                {{ trans('fi.default_item_tax_2_rate') }}: </label>
            {!! Form::select('setting[itemTax2Rate]', $taxRates, config('fi.itemTax2Rate'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_vat_tax_id') !!}">
                {{ trans('fi.enable_vat_tax_id') }}: </label>
            {!! Form::select('setting[clientColumnSettingsVatTaxId]', $yesNoArray, config('fi.clientColumnSettingsVatTaxId'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
</div>