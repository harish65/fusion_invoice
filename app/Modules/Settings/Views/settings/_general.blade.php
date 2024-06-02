@section('javascript')
    @parent
    <script type="text/javascript">
        $().ready(function () {
            $('.btn-check-update').click(function () {
                $.ajax({
                    url: '{{ route('settings.updateCheck') }}',
                    method: 'get',
                    beforeSend: function () {
                        showHideLoaderModal();
                    },
                    success: function (response) {
                        showHideLoaderModal();
                        alertify.success(response.message, 5);
                    },
                    error: function () {
                        showHideLoaderModal();
                        alertify.error('{{ trans('fi.unknown_error') }}', 5);
                    }
                });
            });
        });
    </script>
@stop

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ trans('fi.system_setting_and_ui') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 no-padding">
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.tt_gen_header_title_text') !!}">
                                {{ trans('fi.header_title_text') }}: </label>
                            {!! Form::text('setting[headerTitleText]', config('fi.headerTitleText'), ['class' => 'form-control form-control-sm']) !!}
                        </div>
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.tt_gen_default_company_profile') !!}">
                                {{ trans('fi.default_company_profile') }}: </label>
                            {!! Form::select('setting[defaultCompanyProfile]', $companyProfiles, config('fi.defaultCompanyProfile'), ['class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.tt_gen_address_format') !!}">
                                {{ trans('fi.address_format') }}: </label>
                            {!! Form::textarea('setting[addressFormat]', config('fi.addressFormat'), ['class' => 'form-control form-control-sm', 'rows' => 3]) !!}
                        </div>
                    </div>

                    <div class="col-md-4 no-padding">
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.tt_gen_custom_fields_columns') !!}">
                                {{ trans('fi.custom_fields_column_width') }}: </label>
                            {!! Form::select('setting[customFieldsDisplayColumn]', $customFieldColWidthArray, config('fi.customFieldsDisplayColumn') ? config('fi.customFieldsDisplayColumn') :  12, ['class' => 'form-control form-control-sm']) !!}
                        </div>
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.tt_gen_require_tags_on_client_notes') !!}">
                                {{ trans('fi.require_tags_on_client_notes') }}: </label>
                            {!! Form::select('setting[requireTagsOnClientNotes]', $yesNoArray, config('fi.requireTagsOnClientNotes'), ['class' => 'form-control form-control-sm']) !!}
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_gen_skin') !!}">
                                        {{ trans('fi.skin') }}: </label>
                                    {!! Form::select('setting[skin]', $skins, config('fi.skin'), ['class' => 'form-control form-control-sm']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_gen_top_bar_color') !!}">
                                        {{ trans('fi.top_bar_color') }}: </label>
                                    {!! Form::select('setting[topBarColor]',$topBarColor, config('fi.topBarColor'), ['id' => 'top_bar_color','class' => 'form-control form-control-sm']) !!}
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-md-4 no-padding">
                        <div class="form-group">
                            <label>{{ trans('fi.version') }}: </label>

                            <div class="input-group">
                                {!! Form::text('version', config('fi.version'), ['class' => 'form-control form-control-sm', 'disabled' => 'disabled']) !!}
                                <span class="input-group-append">
                                    <button class="btn btn-sm btn-info btn-check-update"
                                            type="button">{{ trans('fi.check_for_update') }}</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.tt_gen_results_per_page') !!}">
                                {{ trans('fi.results_per_page') }}:</label>
                            {!! Form::select('setting[resultsPerPage]', $resultsPerPage, config('fi.resultsPerPage'), ['class' => 'form-control form-control-sm']) !!}
                        </div>
                        <div class="form-group">
                            <label data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.tt_default_client_type') !!}">
                                {{ trans('fi.default_client_type') }}:
                            </label>
                            {!! Form::select('setting[defaultClientType]', $clientTypes, config('fi.defaultClientType'), ['class' => 'form-control form-control-sm']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ trans('fi.localization_and_timezone') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_gen_language') !!}">
                            {{ trans('fi.language') }}: </label>
                        {!! Form::select('setting[language]', $languages, config('fi.language'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_date_format') !!}">
                            {{ trans('fi.date_format') }}: </label>
                        {!! Form::select('setting[dateFormat]', $dateFormats, config('fi.dateFormat'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_use_24_hour_time_format') !!}">
                            {{ trans('fi.use_24_hour_time_format') }}: </label>
                        {!! Form::select('setting[use24HourTimeFormat]', $yesNoArray, config('fi.use24HourTimeFormat'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_gen_timezone') !!}">
                            {{ trans('fi.timezone') }}: </label>
                        {!! Form::select('setting[timezone]', $timezones, config('fi.timezone'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ trans('fi.currency') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_base_currency') !!}">
                            {{ trans('fi.base_currency') }}: </label>
                        {!! Form::select('setting[baseCurrency]', $currencies, config('fi.baseCurrency'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                    <div class="form-group col-md-4">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_exchange_rate_mode') !!}">
                            {{ trans('fi.exchange_rate_mode') }}: </label>
                        {!! Form::select('setting[exchangeRateMode]', $exchangeRateModes, config('fi.exchangeRateMode'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                    <div class="form-group col-md-4">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_number_of_tax_fields') !!}">
                            {{ trans('fi.number_of_tax_fields') }}: </label>
                        {!! Form::select('setting[numberOfTaxFields]', $numberOfTaxFieldsArray, config('fi.numberOfTaxFields') ? config('fi.numberOfTaxFields') :  2, ['class' => 'form-control form-control-sm']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_quantity_price_decimals') !!}">
                            {{ trans('fi.quantity_price_decimals') }}: </label>
                        {!! Form::select('setting[amountDecimals]', $amountDecimalOptions, config('fi.amountDecimals'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_round_tax_decimals') !!}">
                            {{ trans('fi.round_tax_decimals') }}: </label>
                        {!! Form::select('setting[roundTaxDecimals]', $roundTaxDecimalOptions, config('fi.roundTaxDecimals'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ trans('fi.security') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label data-toggle="tooltip" data-placement="auto"
                               title="{!! trans('fi.tt_gen_use_captcha_in_login') !!}">
                            {{ trans('fi.use_captcha_in_login') }}: </label>
                        {!! Form::select('setting[useCaptchInLogin]', $yesNoArray, config('fi.useCaptchInLogin'), ['class' => 'form-control form-control-sm']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>