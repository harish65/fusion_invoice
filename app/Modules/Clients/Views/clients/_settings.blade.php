<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_active') !!}"> {{ trans('fi.active') }}:</label>
            {!! Form::select('active', ['0' => trans('fi.no'), '1' => trans('fi.yes')], ((isset($editMode) and $editMode) ? null : 1), ['id' => 'active', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    @if($parentClient == true)
        <div class="col-md-4 offset-md-1">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_parent_account') !!}">
                    {{ trans('fi.parent_account') }}: </label>
                {!! Form::select('parent_client_id', $parentClients, null, ['id' => 'parent_client_id', 'class' => 'form-control form-control-sm client-lookup']) !!}
                <div class="form-subtext">
                    {{ trans('fi.leave_empty_no_parent_account') }}
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-4">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_invoices_paid_by') !!}">
                {{ trans('fi.invoices_paid_by') }}: </label>
            {!! Form::select('invoices_paid_by',['' => trans('fi.select_client')] +  $invoicesPaidBy, null, ['id' => 'invoices_paid_by', 'class' => 'form-control form-control-sm client-lookup']) !!}
            <div class="form-subtext">
                {{ trans('fi.leave_empty_client_pays_invoices') }}
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    @if(config('fi.clientColumnSettingsInvoicePrefix') == 1)
        <div class="col-md-2">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_invoice_prefix') !!}">{{ trans('fi.invoice_prefix') }}:</label>
                {!! Form::text('invoice_prefix', null, ['id' => 'invoice_prefix', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
    @if(config('fi.clientColumnSettingsDefaultCurrency') == 1)
        <div class="col-md-2">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_default_currency') !!}">{{ trans('fi.default_currency') }}: </label>
                {!! Form::select('currency_code', $currencies, $client->currency_code ?? config('fi.baseCurrency'), ['id' => 'currency_code', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
    @if(config('fi.clientColumnSettingsLanguage') == 1)
        <div class="col-md-2">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_language') !!}">{{ trans('fi.language') }}: </label>
                {!! Form::select('language', $languages, $client->language ?? config('fi.language'), ['id' => 'language', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>

    @endif
    @if(config('fi.clientColumnSettingsAllowChildAccounts') == 1)
        <div class="col-md-3">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_allow_child_accounts') !!}">{{ trans('fi.allow_child_accounts') }}: </label>
                {!! Form::select('allow_child_accounts',['0' => trans('fi.no'), '1' => trans('fi.yes')],
              (isset($client) and isset($client->allow_child_accounts)) ? $client->allow_child_accounts : 0, ['id' => 'allow_child_accounts', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>

    @endif
    @if(config('fi.clientColumnSettingsThirdPartyBillPayer') == 1)
        <div class="col-md-3">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_third_party_bill_payer') !!}">{{ trans('fi.third_party_bill_payer') }}: </label>
                {!! Form::select('third_party_bill_payer',['0' => trans('fi.no'), '1' => trans('fi.yes')],
             (isset($client) and isset($client->third_party_bill_payer)) ? $client->third_party_bill_payer : 0, ['id' => 'third_party_bill_payer', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
</div>

<div class="row">
    @if(config('fi.clientColumnSettingsTimezone') == 1)
        <div class="col-md-3">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_timezone') !!}">{{ trans('fi.timezone') }}: </label>
                {!! Form::select('timezone', $timezones, config('fi.timezone'), ['id' => 'timezone', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
    @if(config('fi.clientColumnSettingsAutomaticEmailPaymentReceipt') == 1)
        <div class="col-md-3">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_automatic_email_payment_receipts') !!}">{{ trans('fi.automatic_email_payment_receipts') }}: </label>
                {!! Form::select('automatic_email_payment_receipt', ['default' => trans('fi.default'), 'yes' => trans('fi.yes'), 'no' => trans('fi.no')], null, ['class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
    @if(config('fi.clientColumnSettingsAutomaticEmailOnRecurringInvoice') == 1)
        <div class="col-md-3">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_automatic_email_on_recur') !!}">{{ trans('fi.automatic_email_on_recur') }}: </label>
                {!! Form::select('automatic_email_on_recur', ['default' => trans('fi.default'), 'yes' => trans('fi.yes'), 'no' => trans('fi.no')], null, ['id' => 'automatic_email_on_recurring_invoice', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
    @if(config('fi.clientColumnSettingsOnlinePaymentProcessingFee') == 1)
        <div class="col-md-3">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_allow_online_payment_processing_fees') !!}">{{ trans('fi.allow_online_payment_processing_fees') }}: </label>
                {!! Form::select('online_payment_processing_fee', ['default' => trans('fi.default'), 'yes' => trans('fi.yes'), 'no' => trans('fi.no')], null, ['id' => 'online_payment_processing_fee', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-3" id="use_parent_email_container"
         style="display: {{(isset($client->parent_client_id) && !empty($client->parent_client_id)) ? 'block' : 'none'}}">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_email_default') !!}">{{ trans('fi.email_default') }}: </label>
            {!! Form::select('email_default', ['' => trans('fi.client_default'),'use_parent_email' => trans('fi.use_parent_email'), 'use_third_party_bill_payer_email' => trans('fi.use_third_party_bill_payer_email')], null,  ['id' => 'use_parent_email', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <hr>
</div>

<hr>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::checkbox('allow_client_center_login', 1, isset($client->user) ? 1 : '', ['id' => 'allow_client_center_login', 'class' => '']) !!}
            <label for="allow_client_center_login" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_allow_client_center_login') !!}">{{ trans('fi.allow_client_center_login') }} </label>
        </div>
    </div>
</div>

<div class="mb-0 client-login-detail">
    <div class="row p-1">
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ trans('fi.client_name') }}:</label>
                {!! Form::text('cname', isset($client->name) ? $client->name : null , ['id' => 'cname', 'class' => 'form-control form-control-sm', 'disabled' => true]) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ trans('fi.email_address') }}: </label>
                {!! Form::text('cemail', isset($client->client_email) ? $client->client_email : null , ['id' => 'cemail', 'class' => 'form-control form-control-sm', 'disabled' => true]) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ trans('fi.password') }}: </label>
                {!! Form::password('password', ['id' => 'password', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ trans('fi.password_confirmation') }}: </label>
                {!! Form::password('password_confirmation', ['id' => 'password_confirmation',
                'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#parent_client_id').on("select2:select", function (e, source) {
            let selected_parent_id = $(e.currentTarget).val();
            if (selected_parent_id) {
                $('#use_parent_email_container').show();
                $('#use_parent_email').append(`<option value="use_parent_email"> {{trans('fi.use_parent_email')}} </option>`);
            } else {
                $('#use_parent_email_container').hide();
                $('#use_parent_email').prop('checked', false);
            }
        });
        $("#parent_client_id").on("select2:unselect", function (e, source) {
            var $_this = $(this);
            let selected_parent_id = $(e.currentTarget).val();
            let selected_third_party_id = $_this.closest('.row').find('#invoices_paid_by').val();
            if (selected_parent_id || selected_third_party_id) {
                $('#use_parent_email_container').show();
            } else {
                $('#use_parent_email_container').hide();
                $('#use_parent_email').prop('checked', false);
            }
            if (!selected_parent_id) {
                $('#use_parent_email').children().each(function (e) {
                    var $__this = $(this);
                    if ($__this.val() == 'use_parent_email') {
                        $__this.remove();
                    }
                });
            }
        });


        $('#invoices_paid_by').on("select2:select", function (e, source) {
            let selected_third_party_id = $(e.currentTarget).val();
            if (selected_third_party_id) {
                $('#use_parent_email_container').show();
                $('#use_parent_email').append(`<option value="use_third_party_bill_payer_email"> {{trans('fi.use_third_party_bill_payer_email')}} </option>`);

            } else {
                $('#use_parent_email_container').hide();
                $('#use_third_party_email').prop('checked', false);
            }
        });

        $('#invoices_paid_by').on("select2:unselect", function (e, source) {
            var $_this = $(this);
            let selected_third_party_id = $(e.currentTarget).val();
            let selected_parent_id = $_this.closest('.row').find('#parent_client_id').val();
            if (selected_parent_id || selected_third_party_id) {
                $('#use_parent_email_container').show();
            } else {
                $('#use_parent_email_container').hide();
                $('#use_third_party_email').prop('checked', false);
            }
            if (!selected_third_party_id) {
                $('#use_parent_email').children().each(function (e) {
                    var $__this = $(this);
                    if ($__this.val() == 'use_third_party_bill_payer_email') {
                        $__this.remove();
                    }
                });
            }
        });

        $('#allow_client_center_login').click(function () {
            if ($(this).prop("checked") == true) {
                $('.client-login-detail').removeClass('clientcenter-overlay');
                $('.client-login-detail').removeClass('clientcenter-overlay-true');
                $('#password,#password_confirmation').attr('disabled', false);
            } else {
                $('.client-login-detail').addClass('clientcenter-overlay');
                $('.client-login-detail').addClass('clientcenter-overlay-true');
                $('#password,#password_confirmation').val('').attr('disabled', true);
            }
        });

        $('#client_email').change(function () {
            $('#cemail').val($(this).val());
        });

        $('#name').change(function () {
            $('#cname').val($(this).val());
        });
    });

    $(document).ready(function () {
        $('#cname').val($('#name').val());
        $('#cemail').val($('#client_email').val());

        if ($('#allow_client_center_login').prop("checked") == true) {
            $('.client-login-detail').removeClass('clientcenter-overlay');
            $('.client-login-detail').removeClass('clientcenter-overlay-true');
            $('#password,#password_confirmation').attr('disabled', false).val('');
        } else {
            $('.client-login-detail').addClass('clientcenter-overlay');
            $('.client-login-detail').addClass('clientcenter-overlay-true');
            $('#password,#password_confirmation').attr('disabled', true).val('');
        }
        if ($('#parent_client_id').val() || $('#invoices_paid_by').val()) {
            $('#use_parent_email_container').show();
        }
        if (!$('#parent_client_id').val()) {
            $('#use_parent_email').children().each(function (e) {
                var $__this = $(this);
                if ($__this.val() == 'use_parent_email') {
                    $__this.remove();
                }
            });
        }
        if (!$('#invoices_paid_by').val()) {
            $('#use_parent_email').children().each(function (e) {
                var $__this = $(this);
                if ($__this.val() == 'use_third_party_bill_payer_email') {
                    $__this.remove();
                }
            });
        }
    });
</script>
<style>

    .clientcenter-overlay {width: 100%;top: 0;left: 0;right: 0;bottom: 0;background-color: rgba(0, 0, 0, 0.07);z-index: 99999;border-radius: 5px;margin-bottom: 5px;}

    .clientcenter-overlay-true {pointer-events: none;opacity: 0.5;}

</style>