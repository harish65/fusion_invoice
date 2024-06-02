<div class="mt-1 pt-2 pb-2">
        <div class="col-md-6 pl-0">
            <h4>
                <label class="jc-left" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_olp_about') !!}">
                <i class="fas fa-credit-card pr-3"></i>
                {{ trans('fi.online_payments') }} 
                </label> 
            </h4>
        </div>
</div>

@foreach ($merchantDrivers as $driver)
    <h4 style="font-weight: bold; clear: both; margin-top: 20px;">{{ $driver->getName() }}</h4>
    <hr>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>{{ trans('fi.enabled') }}</label>
                {!! Form::select('setting[' . $driver->getSettingKey('enabled') . ']', [0=>trans('fi.no'),1=>trans('fi.yes')], $driver->getSetting('enabled'), ['class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
        @foreach ($driver->getSettings() as $key => $setting)
            @section('translateOnlinePaymentSettingsSection')
            @if (!is_array($setting))
                {{ $settingKey = strtolower($setting) }}
            @else
                {{ $settingKey = strtolower($key) }}
            @endif 
            @switch($settingKey)
                @case('apikey')
                {{ $settingToolTip = trans('fi.tt_ss_olp_api_key') }}
                @break
                @case('mode')
                {{ $settingToolTip = trans('fi.tt_ss_olp_mode') }}
                @break
                @case('clientid')
                {{ $settingToolTip = trans('fi.tt_ss_olp_client_id') }}
                @break
                @case('clientsecret')
                {{ $settingToolTip = trans('fi.tt_ss_olp_client_secret') }}
                @break
                @case('publishablekey')
                {{ $settingToolTip = trans('fi.tt_ss_olp_publishable_key') }}
                @break
                @case('secretkey')
                {{ $settingToolTip = trans('fi.tt_ss_olp_secret_key') }}
                @break
                @default
                {{ $settingToolTip = '' }}
            @endswitch
            @endsection

            <div class="col-md-2">
                <div class="form-group">
                    @if (!is_array($setting))
                        <label data-toggle="tooltip" data-placement="auto" title="{{ $settingToolTip }}">
                            {{ trans('fi.' . \Illuminate\Support\Str::snake($setting)) }}</label>
                        {!! Form::text('setting[' . $driver->getSettingKey($setting) . ']', config('fi.' . $driver->getSettingKey($setting)), ['class' => 'form-control form-control-sm']) !!}
                    @else
                        <label data-toggle="tooltip" data-placement="auto" title="{{ $settingToolTip }}">
                            {{ trans('fi.' . \Illuminate\Support\Str::snake($key)) }}</label>
                        {!! Form::select('setting[' . $driver->getSettingKey($key) . ']', $setting, config('fi.' . $driver->getSettingKey($key)), ['class' => 'form-control form-control-sm']) !!}
                    @endif
                </div>
            </div>
        @endforeach
        <div class="col-md-2">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_olp_payment_button_text') !!}">
                    {{ trans('fi.payment_button_text') }}</label>
                {!! Form::text('setting[' . $driver->getSettingKey('paymentButtonText') . ']', $driver->getSetting('paymentButtonText'), ['class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    </div>
@endforeach

<hr>
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_olp_opp_fees_about') !!}">
                    {{ trans('fi.online_payment_processing_fee') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="enableOppFees" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_olp_enable_opp_fees') !!}">
                                {{ trans('fi.enable_online_payment_processing_fees') }}</label>
                            {!! Form::select('setting[enableOppFees]', $yesNoArray, config('fi.enableOppFees') != '' ? config('fi.enableOppFees') : null, ['id' => 'enableOppFees','class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                        </div>
                        <div class="form-group">
                            <label for="feeName" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_olp_opp_fee_name') !!}">
                                {{ trans('fi.fee_name') }}</label>
                            {!! Form::text('setting[feeName]',(config('fi.feeName') != null ) ? config('fi.feeName') : trans('fi.credit_card_processing_fee') , ['id' => 'feeName','class' => 'form-control form-control-sm' ,'placeholder'=>'Fee Name', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="feePercentage" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_olp_opp_fee_pct') !!}">
                                {{ trans('fi.fee_percentage') }}</label>
                            <div class="input-group">
                                {!! Form::text('setting[feePercentage]',(config('fi.feePercentage') != null ) ? config('fi.feePercentage') : 3 , ['id' => 'feePercentage','aria-describedby' => 'inputGroupPrepend','class' => 'form-control form-control-sm' ,'placeholder'=>'Fee Percentage %', 'autocomplete' => 'off']) !!}
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="feeExplanation" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_olp_opp_fee_explanation') !!}">
                                {{ trans('fi.fee_explanation') }}</label>
                            {!! Form::text('setting[feeExplanation]',(config('fi.feeExplanation') != null ) ? config('fi.feeExplanation') : trans('fi.convenience_fee_for_credit_card_transactions'), ['id' => 'feeExplanation','class' => 'form-control form-control-sm' ,'placeholder'=>'Fee Explanation', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>