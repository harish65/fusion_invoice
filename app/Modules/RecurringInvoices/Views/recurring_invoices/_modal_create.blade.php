@include('layouts._select2')
@include('clients._js_lookup', ['addNew' => $isClientCreate])
@include('recurring_invoices._js_create')

<div class="modal fade" id="create-recurring-invoice" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.create_recurring_invoice') }}</h5>
                <button type="button" class="close modal-create-recurring-invoice-close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" id="user_id">

                    <div class="form-group  client-detail">
                        <label>{{ trans('fi.client') }}</label>
                        {!! Form::select('client_name', $clients, null, ['id' => 'create_client_name', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off', 'style' => 'width:100%;']) !!}
                    </div>

                    <div class="form-group ">
                        <label>{{ trans('fi.company_profile') }}</label>
                        {!! Form::select('company_profile_id', $companyProfiles, config('fi.defaultCompanyProfile'),
                            ['id' => 'company_profile_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.document_number_schemes') }}</label>
                        {!! Form::select('document_number_scheme_id', $documentNumberSchemes, config('fi.invoiceGroup'), ['id' => 'create_document_number_scheme_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.start_date') }}</label>
                        <div class="input-group date" id='create_next_date' data-target-input="nearest">
                            {!! Form::text('next_date', null, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#create_next_date']) !!}
                            <div class="input-group-append"
                                 data-target='#create_next_date' data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.every') }}</label>

                        <div class="row">
                            <div class="col-sm-3">
                                {!! Form::select('recurring_frequency', array_combine(range(1, 90), range(1, 90)), '1', ['id' => 'recurring_frequency', 'class' => 'form-control form-control-sm']) !!}
                            </div>
                            <div class="col-sm-9">
                                {!! Form::select('recurring_period', $frequencies, 3, ['id' => 'recurring_period', 'class' => 'form-control form-control-sm']) !!}
                            </div>
                        </div>

                    </div>

                    <div class="form-group date">
                        <label>{{ trans('fi.stop_date') }}</label>
                        <div class="input-group date" id='create_stop_date' data-target-input="nearest">
                            {!! Form::text('stop_date', null,['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#create_stop_date']) !!}
                            <div class="input-group-append"
                                 data-target='#create_stop_date' data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default modal-create-recurring-invoice-close" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="recurring-invoice-create-confirm" class="btn btn-sm btn-primary">{{ trans('fi.submit') }}</button>
            </div>
        </div>
    </div>
</div>