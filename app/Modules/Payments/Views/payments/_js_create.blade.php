<script type="text/javascript">

    $(function () {
        var importantNoteHeader = '<span style="color:white;"> <span class="fa fa-bell-o fa-2x"'
            + 'style="vertical-align:middle;padding-right:10px;">'
            + '</span>' + '{!! trans('fi.important') !!}' + '</span>';

        $('#modal-enter-payment').modal();

        $("#payment_date").datetimepicker({
            format: dateFormat,
            autoclose: true,
            todayHighlight: true
        });


        function countRemainingBalance() {
            let invoice_balance = parseFloat($('#invoice_balance').data('amount'));
            let entered_amount = parseFloat(currencyUnformat($('#payment_amount').val(), $('#currency_code').val()));
            let remaining_balance = (invoice_balance - entered_amount).toFixed(2);
            return remaining_balance;
        }

        $("#payment_amount").blur(function () {
            var remaining_balance = countRemainingBalance();
            if (remaining_balance < 0) {
                $('#remaining_balance').val(currencyFormat(0.00, $('#currency_code').val()));
            } else {
                $('#remaining_balance').val(currencyFormat(remaining_balance, $('#currency_code').val()));
            }
        });
        @can('payments.create')
        $('#enter-payment-confirm').click(function () {
            var payment_data = {};
            var form_data;
            var custom_fields = {};
            var selectCustomRadioButtonValue = null;

            let entered_amount = parseFloat(standardCurrencyFormat($('#payment_amount').val()));
            if (entered_amount <= 0) {
                alertify.alert().setHeader(importantNoteHeader).set({transition: 'zoom'})
                    .setContent("{!! trans('fi.payment_warning') !!}").showModal();
                return false;
            }

            var $this = $(this);
            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

            $('#payment-custom-fields .custom-form-field').each(function () {


                var fieldName = $(this).data('payments-field-name');
                var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                if (fieldName !== undefined) {
                    if ('file' === inputType) {
                        custom_fields[fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];
                        return true;
                    }

                    if ('select' === inputType) {
                        if ($(this).find('option:selected').length == 0) {
                            custom_fields[fieldName] = '';
                            return true;
                        }
                    }
                    if ('checkbox' === inputType) {
                        custom_fields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                        return true;
                    }

                    if ('radio' === inputType) {
                        if ($(this).prop('checked') == true) {
                            custom_fields[fieldName] = $(this).val();
                            selectCustomRadioButtonValue = $(this).val();
                        }
                        if ($(this).prop('checked') == false && selectCustomRadioButtonValue == null) {
                            custom_fields[fieldName] = 'null';
                        }
                        return custom_fields[fieldName];
                    }

                    custom_fields[fieldName] = $(this).val();
                }

            });

            payment_data['client_id'] = $('#client_id').val();
            payment_data['invoice_id'] = $('#invoice_id').val();
            payment_data['amount'] = $('#payment_amount').val();
            payment_data['remaining_balance'] = $('#remaining_balance').val();
            payment_data['payment_method_id'] = $('#payment_method_id').val();
            payment_data['paid_at'] = $('#payment_date').children().val();
            payment_data['note'] = $('#payment_note').val();
            payment_data['custom'] = custom_fields;
            payment_data['email_payment_receipt'] = ($('#email_payment_receipt').prop('checked')) ? 1 : 0;
            payment_data['currency_code'] = $('#currency_code').val();

            form_data = objectToFormData(payment_data);

            $.ajax({
                url: '{{ route('payments.store') }}',
                method: 'post',
                data: form_data,
                async: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.error) {
                        alertify.error(data.error, 5);
                    }
                },
            }).done(function () {
                window.location = '{!! $redirectTo !!}';
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });
        @endcan

    });

</script>
