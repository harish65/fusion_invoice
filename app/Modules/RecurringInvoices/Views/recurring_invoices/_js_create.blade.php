<script type="text/javascript">

    $(function () {

        $('#create-recurring-invoice').modal();

        $('#create-recurring-invoice').on('shown.bs.modal', function () {
            $("#create_client_name").focus();
        });

        $('.modal-create-recurring-invoice-close').click(function () {
            $('.btn-action-modal').removeClass('disabled');
        });

        $('#create_next_date').datetimepicker({autoclose: true, format: dateFormat , defaultDate: new Date()});
        $('#create_stop_date').datetimepicker({autoclose: true, format: dateFormat});

        $('#recurring-invoice-create-confirm').click(function () {

            $.post('{{ route('recurringInvoices.store') }}', {
                user_id: $('#user_id').val(),
                company_profile_id: $('#company_profile_id').val(),
                client_id: $('#create_client_name').val(),
                document_number_scheme_id: $('#create_document_number_scheme_id').val(),
                next_date: $('#create_next_date').children().val(),
                stop_date: $('#create_stop_date').children().val(),
                recurring_frequency: $('#recurring_frequency').val(),
                recurring_period: $('#recurring_period').val()
            }).done(function (response) {
                window.location = response.url;
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });

        $(document).off('click', ".create-client").on("click", ".create-client", function () {

            var client_name = ($('.select2-search__field').val() != null && $('.select2-search__field').val() != 'undefined') ? $('.select2-search__field').val() : null;

            $('#modal-recurring-invoice-client-create').load('{!! route('recurring.invoice.client.create.modal') !!}', {
                client_name: client_name,
                type:'create'
            })
        });
    });

</script>