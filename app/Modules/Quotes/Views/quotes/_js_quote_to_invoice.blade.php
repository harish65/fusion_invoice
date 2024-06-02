<script type="text/javascript">

    $(function () {
        // Display the create quote modal
        $('#modal-quote-to-invoice').modal('show');

        $("#to_invoice_date").datetimepicker({format: dateFormat, autoclose: true});

        // Creates the invoice
        $('#btn-quote-to-invoice-submit').click(function () {
            $.post('{{ route('quoteToInvoice.store') }}', {
                quote_id: '{{ $quote_id }}',
                client_id: '{{ $client_id }}',
                invoice_date: $('#to_invoice_date').children().val(),
                document_number_scheme_id: $('#to_invoice_document_number_scheme_id').val(),
                user_id: '{{ $user_id }}'

            }).done(function (response) {
                window.location = response.redirectTo;
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