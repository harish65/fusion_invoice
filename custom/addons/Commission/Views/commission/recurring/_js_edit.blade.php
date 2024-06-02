<script type="text/javascript">

    $(function () {

        $('#edit-recurring-commission').modal({backdrop: 'static'});

        $("#commission_stop_date_date_picker").datetimepicker({autoclose: true, format: dateFormat});

        $('#invoice-recurring-edit-commission').click(function () {
            var $btn = $(this).button('loading');
            var action = $("#edit-recurring-commission form").attr('action');
            $.post(action, {
                type_id: $('#type_id').val(),
                user_id: $('#user_id').val(),
                stop_date: $('#commission_stop_date').val(),
                note: $('#note').val(),
                recurring_invoice_item_id: $('#recurring_invoice_item_id').val(),
                amount: $('#amount').val(),
            }).done(function () {
                loadInvoiceCommission();
                $('#edit-recurring-commission').modal('hide');
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
