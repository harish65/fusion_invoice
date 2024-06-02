<script type="text/javascript">

    $(function () {

        $('#edit-commission').modal({backdrop: 'static'});

        $('#invoice-edit-commission').click(function () {
            var $btn = $(this).button('loading');
            var action = $("#edit-commission form").attr('action');
            $.post(action, {
                type_id: $('#type_id').val(),
                user_id: $('#user_id').val(),
                status: $('#commission_status').val(),
                note: $('#note').val(),
                invoice_item_id: $('#invoice_item_id').val(),
                amount: $('#amount').val(),
            }).done(function () {
                $('#edit-commission').modal('hide');
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
