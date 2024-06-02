<script>
    $(function () {
        var triggerSave = false;

        $(document).on("change", "tr.item select[name='name']", function () {
            triggerSave = true;
        });

        $('.btn-save-invoice').click(function () {
            triggerSave = false;
        });

        $(document).on("click", '.create-commission', function (e) {

            var invoice = $(this).data('id');
            var action = $(this).data('action');
            if (triggerSave) {
                $('.btn-save-invoice:first').click()
            }
            $('#modal-placeholder').load(action, function () {
                $('#invoice_item_id').val(invoice)
            });
        });

        $(document).on("click", '.create-recurring-commission', function (e) {
            var invoice = $(this).data('id');
            var action = $(this).data('action');
            if (triggerSave) {
                $('.btn-save-recurring-invoice:first').click()
            }
            $('#modal-placeholder').load(action, function () {
                $('#reccuring_invoice_item_id').val(invoice)
            });
        });

        $(document).on("click", '.edit-commission', function (e) {
            e.preventDefault()
            var invoice = $(this).attr('href');
            $('#modal-placeholder').load(invoice)
        });

        $(document).on("click", '.commission-delete', function (e) {
            e.preventDefault()
            var index = $(this).parents('.index').length
            var _this = $(this);
            _this.addClass('delete-invoices-commission-active');

            $('#modal-placeholder').load('{!! route('invoices.commission.delete.modal') !!}', {
                    action: _this.data('action'),
                    returnURL: '{{route('invoices.index')}}',
                    modalName: 'invoices-commission',
                    index: index,
                    message: "{{ trans('Commission::lang.confirm_delete_commission_type') }}",
                    isReload: false
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });

    });

    function loadInvoiceCommission() {
        var action = $(".commission_uri").val();
        if (typeof action !== 'undefined') {
            $.ajax({
                url: $(".commission_uri").val(),
                success: function (data) {
                    $(".renderCommission").html(data)
                }
            });
        } else {
            window.location.reload();
        }
    }
</script>