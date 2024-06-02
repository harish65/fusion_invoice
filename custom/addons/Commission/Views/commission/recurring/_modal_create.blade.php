@include('commission.recurring._js_create')
<div class="modal fade" id="create-recurring-commission">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoice_create_title">{{ trans('Commission::lang.create_recurring') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <div class="row">

                    <div class="col-md-12">

                        {!! Form::model($commission, ['class' => ['form-horizontal']]) !!}

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('Commission::lang.invoice_item') }}</label>

                            <div class="col-sm-12">
                                {!! Form::select('recurring_invoice_item_id', $items, null, ['id' => 'recurring_invoice_item_id', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('Commission::lang.user') }}</label>

                            <div class="col-sm-12">
                                {!! Form::select('user_id', $users, null, ['id' => 'user_id', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('Commission::lang.commission_type') }}</label>

                            <div class="col-sm-12">
                                {!! Form::select('type_id', $commissionType, null, ['id' => 'type_id', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>


                        <div class="form-group" id="commission_amount" style="display: none">
                            <label class="col-sm-3 control-label">{{ trans('fi.amount') }}</label>

                            <div class="col-sm-12">
                                {!! Form::text('amount','', ['id' => 'amount', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('Commission::lang.note') }}</label>

                            <div class="col-sm-12">
                                {!! Form::textarea('note', '', ['id' =>'note', 'class' => 'form-control form-control-sm', 'rows' => '3']) !!}
                            </div>
                        </div>

                        <div class="form-group p-2">
                            <div class="input-group date">
                                <label class="document-options-label">{{ trans('Commission::lang.stop_date') }}</label>
                                <div class="input-group date document-options-input-group"
                                     id='commission_stop_date_label'
                                     data-target-input="nearest">
                                    {!! Form::text('stop_date', date(config('fi.dateFormat')), ['id' =>'commission_stop_date', 'class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#commission_stop_date_label']) !!}
                                    <div class="input-group-append"
                                         data-target='#commission_stop_date_label' data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {!! Form::close() !!}

                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="invoice-recurring-create-commission"
                        class="btn btn-sm btn-primary">{{ trans('fi.submit') }}
                </button>
            </div>
        </div>
    </div>
</div>
