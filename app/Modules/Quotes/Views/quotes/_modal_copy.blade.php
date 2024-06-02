@include('layouts._select2')
@include('clients._js_lookup', ['addNew' => $isClientCreate])
@include('quotes._js_copy')

<div class="modal fade" id="modal-copy-quote">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.copy') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label>{{ trans('fi.client') }}</label>
                        {!! Form::select('client_name', $clients, $quote->client_id, ['id' => 'copy_client_name', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off', 'style'=>"width: 100%;"]) !!}
                    </div>

                    <div class="input-group date">
                        <label>{{ trans('fi.date') }}</label>
                        <div class="input-group date" id="copy_quote_date" data-target-input="nearest">
                            {!! Form::text('quote_date', null,['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker', 'autocomplete' => 'off' ,'data-target' => "#copy_quote_date"]) !!}
                            <div class="input-group-append" data-target="#copy_quote_date" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>{{ trans('fi.company_profile') }}</label>
                        {!! Form::select('company_profile_id', $companyProfiles, config('fi.defaultCompanyProfile'),
                            ['id' => 'copy_company_profile_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.document_number_scheme') }}</label>
                        {!! Form::select('document_number_scheme_id', $documentNumberSchemes, $quote->document_number_scheme_id, ['id' => 'copy_document_number_scheme_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-copy-quote-submit"
                        class="btn btn-sm btn-primary">{{ trans('fi.submit') }}</button>
            </div>
        </div>
    </div>
</div>