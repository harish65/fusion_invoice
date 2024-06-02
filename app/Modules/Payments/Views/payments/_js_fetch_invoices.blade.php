<script src='{{ asset('assets/plugins/validate_numeric_input.js') }}'></script>
<style>
    .invoice-table {
        max-height: 300px;
        overflow-y: scroll;
        overflow-x: hidden;
    }
    ::-webkit-scrollbar {
        -webkit-appearance: none;
        width: 7px;
    }

    ::-webkit-scrollbar-thumb {
        border-radius: 4px;
        background-color: rgba(0, 0, 0, .5);
        box-shadow: 0 0 1px rgba(255, 255, 255, .5);
    }
</style>
<script type="text/javascript">
    $(function () {

        var importantNoteHeader = '<span style="color:white;"> <span class="fa fa-bell-o fa-2x"'
            + 'style="vertical-align:middle;padding-right:10px;">'
            + '</span>' + '{!! trans('fi.important') !!}' + '</span>';

        function submitFetchInvoicesForm() {
            $.post('{{ route('payments.storePayment') }}', $('#fetch-invoices-form').serialize(), {
                // id: $_this.data('contact-id')
            }).done(function (response) {
                if (response.success == true) {
                    $("#modal-fetch-invoices").modal("hide");
                    window.location = '{{ route('payments.index') }}';
                }
                else {
                    showAlertifyErrors($.parseJSON(response.responseText).message);
                }
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).message);
            });
        }

        $('#modal-fetch-invoices').modal();
        $('#modal-fetch-invoices').on('hidden.bs.modal', function (e) {
            location.reload();
        });

        $('#confirm-payment-invoices').on("click", function () {

            var amount = $("input[name='amount']").val();
            $("input[name='amount']").val(systemCurrencyFormat(amount));

            let that = $(this);
            let remaining_balance = countRemainingBalance();

            if (remaining_balance <= 0) {
                var confirm_remaining_balance = "{!! trans('fi.confirm_full_payment_applied') !!}";
                var header = '{!! trans('fi.remaining_payment_balance') !!}';
                var color = 'success';
            } else {
                var confirm_remaining_balance = "{!! trans('fi.confirm_remaining_balance') !!}";
                confirm_remaining_balance = confirm_remaining_balance.replace(':value', "{{getCurrencySign(config('fi.baseCurrency'))}}" + ' ' + systemCurrencyFormat(remaining_balance));
                var header = '{!! trans('fi.remaining_payment_balance') !!}';
                var color = 'danger';
            }

            if ($("input:checkbox:checked[id^='invoice_selection_']").length > 0) {
                if (remaining_balance) {
                    $('#modal-confirm-payment-invoices').load('{!! route('confirm.payments.modal') !!}', {
                            action: '{{ route('payments.storePayment') }}',
                            header: header,
                            confirmRemainingBalance: confirm_remaining_balance,
                            modalName: 'payments',
                            paymentIntent: false,
                            color: color,
                            returnURL: '{{route('payments.index')}}'
                        },
                        function (response, status, xhr) {
                            if (status == "error") {
                                var response = JSON.parse(response);
                                alertify.error(response.message);
                            }
                        }
                    );
                }
            } else {

                $('#modal-confirm-payment-invoices').load('{!! route('confirm.payments.modal') !!}', {
                        action: '{{ route('payments.storePayment') }}',
                        header: '{!! trans('fi.delete-confirm') !!}',
                        confirmRemainingBalance: "{!! trans('fi.apply_pre_payment_warning') !!}",
                        modalName: 'payments',
                        paymentIntent: true,
                        color: 'danger',
                        returnURL: '{{route('payments.index')}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            }

        });

        const selectInvoice = (invoice_id) => {

            return new Promise((resolve, reject) => {
                let currency = $('#invoice_selection_' + invoice_id).data('currency');
                let fetch_full_amount = parseFloat($('#invoice_selection_' + invoice_id).data('amount'));

                let remaining_balance = parseFloat($('#remaining_balance').data('amount'));

                if ($('#invoice_selection_' + invoice_id).prop("checked")) {
                    if (remaining_balance >= fetch_full_amount) {
                        $('#paid_amount_' + invoice_id).val(currencyFormat(fetch_full_amount.toFixed(2), currency)).prop("disabled", false);
                    }
                    else {
                        $('#paid_amount_' + invoice_id).val(currencyFormat(remaining_balance, currency)).prop("disabled", false);
                    }
                } else {
                    $('#paid_amount_' + invoice_id).val(currencyFormat(0.00, currency)).prop("disabled", true);
                }
                resolve();
            });
        };

        $("input:checkbox[id^='invoice_selection_']").click(function () {
            selectInvoice($(this).data('id')).then(() => countRemainingBalance());
        });

        $("input[id^='paid_amount_']").blur(function () {
            var remaining_balance = countRemainingBalance();
            if (remaining_balance < 0) {
                $(this).val(systemCurrencyFormat(0.00));
                alertify.alert().setHeader(importantNoteHeader).set({transition: 'zoom'})
                    .setContent("{!! trans('fi.more_figure_then_total_payment') !!}").showModal();
                countRemainingBalance();
            }
        });

        function countRemainingBalance() {
            let total_paid = 0.00;
            let currency = $('#amount').data('currency');
            let entered_amount = parseFloat($('#amount').data('amount')).toFixed(2);
            $("input:checkbox:checked[id^='invoice_selection_']").each(function () {
                let invoice_id = $(this).data('id');
                if ($('#paid_amount_' + invoice_id).val()) {
                    let current_paid_amount = currencyUnformat($('#paid_amount_' + invoice_id).val(), $('#paid_amount_' + invoice_id).data('currency'));
                    total_paid = parseFloat(total_paid) + parseFloat(current_paid_amount);
                }
            });
            remaining_balance = (entered_amount - total_paid).toFixed(2);
            $('#remaining_balance').val(currencyFormat(remaining_balance, currency));
            $('#remaining_balance').data('amount', remaining_balance);
            return remaining_balance;
        }
    });

</script>
