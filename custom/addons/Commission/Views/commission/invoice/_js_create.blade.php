<script type="text/javascript">

    $(function () {

        $('#create-commission').modal({backdrop: 'static'});

        $('#invoice-create-commission').click(function () {
            var $btn = $(this).button('loading');
            var $this = $(this);
            $this.prop("disabled", true).html($this.data('loading-text'));
            $.post('{{ route('invoice.commission.store') }}', {
                type_id: $('#type_id').val(),
                user_id: $('#user_id').val(),
                status: $('#commission_status').val(),
                note: $('#note').val(),
                amount: $('#amount').val(),
                invoice_item_id: $('#invoice_item_id').val(),
            }).done(function () {
                $('#create-commission').modal('hide')
                loadInvoiceCommission();
            }).fail(function (response) {
                $btn.button('reset');
                $this.prop("disabled", false).html($this.data('text'));
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
