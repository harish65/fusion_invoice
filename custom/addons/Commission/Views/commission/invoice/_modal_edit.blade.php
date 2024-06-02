@include('commission.invoice._js_edit')
<div class="modal fade" id="edit-commission">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoice_create_title">{{ trans('Commission::lang.edit')  }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <div class="row">
                    <div class="col-md-12">
                        {!! Form::model($commission, ['route' => ['invoice.commission.update', $commission->id],'class'=>'form-horizontal']) !!}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('Commission::lang.invoice_item') }}</label>

                            <div class="col-sm-12">
                                {!! Form::select('invoice_item_id', $items,$commission->invoice_item_id, ['id' => 'invoice_item_id', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('Commission::lang.user') }}</label>

                            <div class="col-sm-12">
                                {!! Form::select('user_id', $users, $commission->user_id, ['id' => 'user_id', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ trans('Commission::lang.commission_type') }}</label>

                            <div class="col-sm-12">
                                {!! Form::select('type_id', $commissionType, $commission->type_id, ['id' => 'type_id', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>

                        <div class="form-group" id="commission_amount"
                             style="{{ $commission->type->method == 'manual_entry' ? 'display: block' : '' }}">
                            <label class="col-sm-3 control-label">{{ trans('fi.amount') }}</label>

                            <div class="col-sm-12">
                                {!! Form::text('amount',$commission->amount, ['id' => 'amount', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('Commission::lang.note') }}</label>

                            <div class="col-sm-12">
                                {!! Form::textarea('note',$commission->note, ['id' =>'note', 'class' => 'form-control form-control-sm', 'rows' => '3']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ trans('Commission::lang.status') }}</label>

                            <div class="col-sm-12">
                                {!! Form::select('status', $status, $commission->status,['id' => 'commission_status', 'class' => 'form-control form-control-sm']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}

                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="invoice-edit-commission"
                        class="btn btn-sm btn-primary">{{ trans('fi.submit') }}
                </button>
            </div>
        </div>
    </div>
</div>
