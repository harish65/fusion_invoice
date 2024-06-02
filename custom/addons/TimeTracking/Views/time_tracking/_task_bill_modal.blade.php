<script type="text/javascript">
    $(function () {

        $('#modal-bill-task').modal();

        var howToBill = $('#how_to_bill');
        var invoiceCount = '{{ $invoiceCount }}';

        billOptions(howToBill.val(), invoiceCount);

        howToBill.change(function () {
            billOptions($(this).val(), invoiceCount);
        });

        $('#btn-submit-bill').click(function() {

            $.post("{{ route('timeTracking.bill.store') }}", {
                how_to_bill: howToBill.val(),
                project_id: '{{ $project->id }}',
                document_number_scheme_id: $('#document_number_scheme_id').val(),
                invoice_id: $('#invoice_id').val(),
                task_ids: JSON.stringify({{ $taskIds }})
            }, function(response) {
                window.location = response;
            });

        });

        function billOptions(howToBill, invoiceCount) {

            $('#div-bill-new').hide();
            $('#div-bill-existing').hide();

            $('#div-bill-' + howToBill).show();

            if (howToBill == 'existing' && invoiceCount == 0) {
                $('#btn-submit-bill').addClass('disabled');
            }
            else {
                $('#btn-submit-bill').removeClass('disabled');
            }
        }
    });
</script>

<div class="modal fade" id="modal-bill-task">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('TimeTracking::lang.how_to_bill') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    {!! Form::select('how_to_bill', ['new' => trans('TimeTracking::lang.bill_to_new_invoice'), 'existing' => trans('TimeTracking::lang.bill_to_existing_invoice')], null, ['id' => 'how_to_bill', 'class' => 'form-control form-control-sm']) !!}
                </div>

                <div id="div-bill-new" style="display: none;">

                    <div class="form-group">

                        <label>{{ trans('fi.document_number_schemes') }}:</label>
                            {!! Form::select('document_number_scheme_id', $documentNumberSchemes, config('fi.invoiceGroup'),
                            ['id' => 'document_number_scheme_id', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                </div>

                <div id="div-bill-existing" style="display: none;">

                    <div class="form-group">
                        <label>{{ trans('TimeTracking::lang.choose_invoice_to_bill') }}:</label>
                        {!! Form::select('invoice_id', $invoices, null, ['class' => 'form-control form-control-sm', 'id' => 'invoice_id']) !!}
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" class="btn btn-sm btn-primary" id="btn-submit-bill">{{ trans('fi.submit') }}</button>
            </div>
        </div>
    </div>
</div>