<script type="text/javascript">

    $(function () {

        $('#modal-copy-recurring-invoice').modal();

        $('#modal-copy-recurring-invoice').on('shown.bs.modal', function () {
            $("#client_name").focus();
        });

        var settings = {
            placeholder: '{{ trans('fi.select_client') }}',
            allowClear: true,
            language: {
                noResults: function () {
                    return '<li><a href="javascript:void(0)" class="text-primary create-client btn-sm"><i class="fa fa-plus"></i> {{ trans('fi.add-new-client') }}</a></li>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        };

        $('#copy_client_name').select2(settings);

        $("#copy_next_date").datetimepicker({format:dateFormat,defaultDate:new Date(),autoclose: true});
        $("#copy_stop_date").datetimepicker({format:dateFormat,autoclose: true});

        $(document).off('click',".create-client").on("click", ".create-client", function () {

            var select2_value = $(this).closest('.select2-results').prev().children().val();
            var client_name = (select2_value != null && select2_value != 'undefined') ? select2_value : null;

            $('#modal-recurring-invoice-client-copy').load('{!! route('quotes.client.create.modal') !!}', {
                client_name: client_name,
                type:'copy'
            })
        });

        // Creates the recurringInvoice
        $('#btn-copy-recurring-invoice-submit').click(function () {
            $.post('{{ route('recurringInvoiceCopy.store') }}', {
                recurring_invoice_id: '{{ $recurringInvoice->id }}',
                client_id: $('#copy_client_name').val(),
                company_profile_id: $('#copy_company_profile_id').val(),
                document_number_scheme_id: $('#copy_document_number_scheme_id').val(),
                user_id: '{{ auth()->user()->id }}',
                next_date: $('#copy_next_date').children().val(),
                recurring_frequency: $('#copy_recurring_frequency').val(),
                recurring_period: $('#copy_recurring_period').val(),
                stop_date: $('#copy_stop_date').children().val()
            }).done(function (response) {
                window.location = response.url;
            }).fail(function (response) {
                if (response.status == 400) {
                    showAlertifyErrors($.parseJSON(response.responseText).errors);
                }
                else {
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
        });
    });

</script>