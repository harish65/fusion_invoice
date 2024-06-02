@include('layouts._select2')
@include('clients._js_lookup', ['addNew' => $isClientCreate])
@include('invoices._js_create')

<div class="modal fade" id="create-invoice" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="invoice_create_title">{{ trans('fi.create_invoice') }}</h4>
                <button type="button" class="close modal-create-invoice-close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" id="user_id">

                    <div class="form-group">
                        <div class="pull-right" style="margin-right: 17px;">
                            <label class="radio-inline text-bold col-lg-pull-0">{!! Form::radio('type', 'invoice', true, ['class' => 'add-line-item']) !!} {{ trans('fi.invoice') }}</label>
                            <label class="radio-inline text-bold col-lg-pull-0 pl-3 cm-pink">{!! Form::radio('type', 'credit_memo', false, ['class' => 'add-line-item']) !!} {{ trans('fi.credit_memo') }}</label>
                        </div>
                    </div>
                    <div class="form-group client-detail">
                        <label>{{ trans('fi.client') }}</label>
                        {!! Form::select('client_name', $clients, null, ['id' => 'create_client_name', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off', 'style'=>"width: 100%;"]) !!}
                    </div>

                    <div class="form-group date">
                        <label>{{ trans('fi.date') }}</label>

                        <div class="input-group date" id='create_invoice_date' data-target-input="nearest">
                            {!! Form::text('invoice_date', null, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#create_invoice_date']) !!}
                            <div class="input-group-append"
                                 data-target='#create_invoice_date' data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.company_profile') }}</label>
                        {!! Form::select('company_profile_id', $companyProfiles, config('fi.defaultCompanyProfile'),
                            ['id' => 'company_profile_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.document_number_scheme') }}</label>
                        {!! Form::select('document_number_scheme_id', $documentNumberSchemes['invoice'], config('fi.invoiceGroup'),
                            ['id' => 'create_document_number_scheme_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default modal-create-invoice-close"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="invoice-create-confirm"
                        class="btn btn-sm btn-primary">{{ trans('fi.submit') }}
                </button>
            </div>
        </div>
    </div>
</div>