<script type="text/javascript">

    $(function () {
        $('#create-expense-bill').modal();

        $('.add-line-item').change(function () {
            if ($('input[name=add_line_item]:checked').val() == 1) {
                $('#line-item-options').show();
            }
            else {
                $('#line-item-options').hide();
            }
        });

        $('#btn-create-expense-bill-confirm').click(function () {
            $.post("{{ route('expenseBill.store') }}", {
                id: {{ $expense->id }},
                invoice_id: $('#invoice_id').val(),
                item_name: $('#item_name').val(),
                item_description: $('#item_description').val(),
                add_line_item: $('input[name=add_line_item]:checked').val()
            }).done(function () {
                window.location = '{{ $redirectTo }}';
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });
    });
</script>

<div class="modal fade" id="create-expense-bill">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.bill_this_expense') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form>

                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" id="user_id">

                    @if ($invoices)
                        <div class="form-group">
                            <label>* {{ trans('fi.label_invoice') }}:</label>
                            {!! Form::select('invoice_id', $invoices, null, ['id' => 'invoice_id', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{!! Form::radio('add_line_item', 1, true, ['class' => 'add-line-item']) !!} {{ trans('fi.add_line_item_to_invoice') }}</label><br>
                            <label>{!! Form::radio('add_line_item', 0, false, ['class' => 'add-line-item']) !!} {{ trans('fi.do_not_add_line_item_to_invoice') }}</label>
                        </div>

                        <div id="line-item-options">
                            <div class="form-group">
                                <label>* {{ trans('fi.label_item_name') }}:</label>
                                {!! Form::text('item_name', $expense->category->name, ['id' => 'item_name', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.label_item_description') }}:</label>
                                {!! Form::textarea('item_description', $expense->description, ['id' => 'item_description', 'class' => 'form-control form-control-sm']) !!}
                            </div>
                        </div>
                    @else
                        <p>{{ trans('fi.no_open_invoices') }}</p>
                    @endif

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                @if ($invoices)
                    <button type="button" id="btn-create-expense-bill-confirm" class="btn btn-sm btn-primary">{{ trans('fi.submit') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>