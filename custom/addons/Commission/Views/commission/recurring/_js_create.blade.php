<script type="text/javascript">

    $(function () {

        $('#create-recurring-commission').modal({backdrop: 'static'});

        $("#commission_stop_date_label").datetimepicker({ autoclose: true, format: dateFormat });

        $('#invoice-recurring-create-commission').click(function () {
            var $btn = $(this).button('loading');
            $.post('{{ route('recurring.invoice.commission.store') }}', {
                type_id: $('#type_id').val(),
                user_id: $('#user_id').val(),
                stop_date: $('#commission_stop_date').val(),
                note: $('#note').val(),
                amount: $('#amount').val(),
                recurring_invoice_item_id: $('#recurring_invoice_item_id').val(),
            }).done(function () {
                $('#create-recurring-commission').modal('hide')
                loadInvoiceCommission();
            }).fail(function (response) {
                $btn.button('reset');
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });

        $("#type_id").change(function () {
            $.post('{{ route('invoice.commission.type.commissiontypes') }}', {
                type_id: $(this).val()
            }).done(function (data) {
                if (data.method == 'manual_entry') {
                    $("#commission_amount").show()
                } else {
                    $("#commission_amount").hide()
                }
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        })

        $("#type_id").change();

    });

</script>
