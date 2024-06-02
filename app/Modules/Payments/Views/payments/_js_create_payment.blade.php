<script src='{{ asset('assets/plugins/validate_numeric_input.js') }}'></script>
<script type="text/javascript">

    $(function () {
        var settings = {
            placeholder: '{{ trans('fi.select_client') }}',
            allowClear: true,
            escapeMarkup: function (markup) {
                return markup;
            }
        };
        $('.client-lookup').select2(settings);

        $('#create-payment').modal();

        $('.modal-create-payment-close').click(function () {
            $('.btn-action-modal').removeClass('disabled');
        });

        $('#create-payment-confirm').on("click", function () {
            let that = $(this);
            that.prop("disabled", true).html(that.data('loading-text'));
            if ($('#payment_intent').val() == 'for_invoices') {
                $.post('{{ route('payments.capturePaymentDetail') }}', $('#create-payment-form').serialize())
                    .done(function (response) {
                        if (response.success == true) {
                            $("#create-payment").modal("hide");
                            $('#modal-placeholder').load('{{ route('payments.fetchInvoicesList') }}');
                        }
                    }).fail(function (response) {
                    showAlertifyErrors($.parseJSON(response.responseText).errors);
                    that.prop("disabled", false).html(that.data('text'));
                });

            } else {
                $.post('{{ route('payments.storePayment') }}', $('#create-payment-form').serialize()).done(function (response) {
                    if (response.success == true) {
                        $("#create-payment").modal("hide");
                        window.location = '{{ route('payments.index') }}';
                    }
                }).fail(function (response) {
                    showAlertifyErrors($.parseJSON(response.responseText).errors);
                    that.prop("disabled", false).html(that.data('text'));
                });
            }
        });

        $("#paid_at").datetimepicker({
            autoclose: true,
            format: dateFormat,
            todayHighlight: true,
            defaultDate: new Date()
        });


        $('#payment_intent').change(function () {
            if ($(this).val() == 'for_invoices') {
                $('#create-payment-confirm').addClass("create-payment-apply-invoices");
                $('#create-payment-confirm').html("{{ trans('fi.apply_to_invoices') }}");
            } else {
                $('#create-payment-confirm').removeClass("create-payment-apply-invoices");
                $('#create-payment-confirm').html("{{ trans('fi.save') }}");
            }
        });

        $('.client-lookup').change(function () {
            var url = '{{ route('clients.emailPaymentReceipt') }}' + '/' + $(this).val();
            $.get(url).done(function (response) {
                if (response.email_receipt == true) {
                    $('#email_payment_receipt').prop('checked', true);
                } else {
                    $('#email_payment_receipt').prop('checked', false);
                }
                $('#currency_code').val(response.currency_code);
            });
        });

    });
</script>
