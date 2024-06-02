@include('quotes._js_quote_to_invoice')

<div class="modal fade" id="modal-quote-to-invoice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.quote_to_invoice') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="input-group date">
                        <label>{{ trans('fi.date') }}</label>
                        <div class="input-group date" id='to_invoice_date' data-target-input="nearest">
                            {!! Form::text('invoice_date', $invoice_date,  ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#to_invoice_date']) !!}
                            <div class="input-group-append"
                                 data-target='#to_invoice_date' data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>
                    </div>


                    <div class="form-group">
                        <label>{{ trans('fi.document_number_schemes') }}</label>
                        {!! Form::select('document_number_scheme_id', $document_number_schemes, config('fi.invoiceGroup'), ['id' => 'to_invoice_document_number_scheme_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-quote-to-invoice-submit"
                        class="btn btn-sm btn-primary">{{ trans('fi.submit') }}</button>
            </div>
        </div>
    </div>
</div>