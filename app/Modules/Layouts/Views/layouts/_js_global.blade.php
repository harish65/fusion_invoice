<script type="text/javascript">

    var all_currencies = @json(isset($allCurrencies) ? $allCurrencies : []);

    function currencyUnformat(input, currency) {
        let matchCurrency = all_currencies.find(c => c.code == currency);
        let cleanInput = input;
        cleanInput = cleanInput.replaceAll(matchCurrency.decimal, 'D');
        cleanInput = cleanInput.replaceAll(matchCurrency.thousands, '');
        cleanInput = cleanInput.replaceAll('D', '.');
        return cleanInput;
    }

    function currencyFormat(input, currency) {
        let matchCurrency = all_currencies.find(c => c.code == currency);
        let cleanInput = (input.toString().includes('.')) ? input.toString() : input + '.00';
        var numParts = cleanInput.split(".");
        numParts[0] = numParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, matchCurrency.thousands);
        return numParts.join(matchCurrency.decimal);
    }

    function currencyWithSymbolFormat(input, currency) {
        let matchCurrency = all_currencies.find(c => c.code == currency);
        let cleanInput = (input.toString().includes('.')) ? input.toString() : input;
        var numParts = cleanInput.split(matchCurrency.decimal);
        numParts[0] = numParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, matchCurrency.thousands);
        if (numParts[1].length > 2) {
            numParts[1] = numParts[1].substr(0, 2);
        } else if (numParts[1].length == 1) {
            numParts[1] = numParts[1] + '0';
        }
        return matchCurrency['symbol'] + ' ' + numParts.join(matchCurrency.decimal);
    }

    function showAlertifyErrors(errors) {

        if (errors == null) {
            return;
        }

        $.each(errors, function (id, message) {
            alertify.error(message[0], 5);
        });

    }

    function showErrors(errors, placeholder) {

        $('.input-group.has-error').removeClass('has-error');
        $(placeholder).html('');
        if (errors == null && placeholder) {
            return;
        }

        $.each(errors, function (id, message) {
            if (id) $('#' + id).parents('.input-group').addClass('has-error');
            if (placeholder) $(placeholder).append('<div class="alert alert-danger">' + message[0] + '</div>');
        });

    }

    function clearErrors() {
        $('.input-group.has-error').removeClass('has-error');
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            'delay': {show: 1100, hide: 100}
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        @can('quotes.create')
        $('.create-quote').click(function () {
            var clientName = $(this).data('unique-name');
            var clientId = $(this).data('client-id');


            $('#modal-placeholder').load('{{ route('quotes.create') }}', function () {
                $('#create_client_name').val(clientName).trigger('change');
                if (clientId != null) {
                    if (clientId != 'undefined') {
                        $('#create_client_name').val(clientId).trigger('change');
                        $('.client-detail').hide();
                    }
                }
            });
        });
        @endcan

        @can('invoices.create')
        $('.create-invoice').click(function () {

            var clientName = $(this).data('unique-name');
            var clientId = $(this).data('client-id');

            $('#modal-placeholder').load('{{ route('invoices.create') }}', function () {

                $('#create_client_name').val(clientName).trigger('change');

                if (clientId != null) {
                    if (clientId != 'undefined') {
                        $('#create_client_name').val(clientId).trigger('change');
                        $('.client-detail').hide();
                    }
                }
            });
        });
        @endcan

        @can('recurring_invoices.create')
        $('.create-recurring-invoice').click(function () {
            var clientName = $(this).data('unique-name');
            var clientId = $(this).data('client-id');
            $('#modal-placeholder').load('{{ route('recurringInvoices.create') }}', function () {
                $('#create_client_name').val(clientName).trigger('change');
                if (clientId != null) {
                    if (clientId != 'undefined') {
                        $('#create_client_name').val(clientId).trigger('change');
                        $('.client-detail').hide();
                    }
                }
            });
        });
        @endcan

        $('.btn-action-modal').click(function () {
            $(this).addClass('disabled');
        });

        @can('payments.create')
        $('.create-payment').click(function () {
            $('#modal-placeholder').load('{{ route('payments.createPayment') }}');
        });
        @endcan

        @can('payments.update')
        $('.edit-payment').click(function () {
            $('#modal-placeholder').load($(this).data('action'));
        });
        @endcan

        @can('invoices.view')
        $('.payment-applications').click(function () {
            $('#modal-placeholder').load($(this).data('action'));
        });
        @endcan

        $(document).on('click', '.email-quote', function () {
            $('#modal-placeholder').load('{{ route('quoteMail.create') }}', {
                quote_id: $(this).data('quote-id'),
                redirectTo: $(this).data('redirect-to')
            }, function (response, status, xhr) {
                if (status == 'error') {
                    alertify.error('{{ trans('fi.problem_with_email_template') }}');
                }
            });
        });

        $(document).off('click', '.email-invoice').on('click', '.email-invoice', function () {
            $('#modal-placeholder').load('{{ route('invoiceMail.create') }}', {
                invoice_id: $(this).data('invoice-id'),
                redirectTo: $(this).data('redirect-to')
            }, function (response, status, xhr) {
                if (status == 'error') {
                    alertify.error('{{ trans('fi.problem_with_email_template') }}');
                }
            });
        });

        $(document).on('click', '#user_notification', function () {
            $('#modal-placeholder').load('{{ route('notifications.userNotifications') }}', function (response, status, xhr) {
                if (status == 'error') {
                    alertify.error('{{ trans('fi.problem_with_email_template') }}');
                }
            });
        });

        @can('payments.create')
        $(document).on('click', '.enter-payment', function () {
            $('#modal-placeholder').load('{{ route('payments.create') }}', {
                invoice_id: $(this).data('invoice-id'),
                invoice_balance: $(this).data('invoice-balance'),
                redirectTo: $(this).data('redirect-to')
            });
        });
        @endcan

        @can('payments.update')
        $(document).on('click', '.btn-edit-payment-note', function () {
            $('#modal-placeholder').load('{{ route('payments.note.edit') }}', {
                id: $(this).data('payment-invoice-id'),
                invoice_id: $(this).data('invoice-id')
            });
        });
        @endcan

        $('#bulk-select-all').click(function () {
            if ($(this).prop('checked')) {
                $('.bulk-record').prop('checked', true);
                if ($('.bulk-record:checked').length > 0) {
                    $('.bulk-actions').show();
                }
            } else {
                $('.bulk-record').prop('checked', false);
                $('.bulk-actions').hide();
            }
        });

        $('.bulk-record').click(function () {
            if ($('.bulk-record:checked').length > 0) {
                $('.bulk-actions').show();
            } else {
                $('.bulk-actions').hide();
                $('#bulk-select-all').prop('checked', false);
            }

            if ($(this).prop('checked')) {
                var isAllChecked = 1;

                $('.bulk-record').each(function () {
                    if (!this.checked)
                        isAllChecked = 0;
                });

                if (isAllChecked == 1) {
                    $('#bulk-select-all').prop('checked', true);
                }
            } else {
                $('#bulk-select-all').prop('checked', false);
            }
        });

        $('.bulk-actions').hide();

    });

    function resizeIframe(obj, minHeight) {
        obj.style.height = '';
        var height = obj.contentWindow.document.body.scrollHeight;

        if (height < minHeight) {
            obj.style.height = minHeight + 'px';
        } else {
            obj.style.height = (height + 50) + 'px';
        }
    }

    function resizeIframeSection(obj, minHeight) {
        obj.style.height = '';
        var height = obj.contentWindow.document.body.scrollHeight;
        if (height < minHeight) {
            height = minHeight + 'px';
        } else {
            height = (height + 95) + 'px';
        }
        $('.iframe-content').css("height", height);
    }

    function standardCurrencyFormat(value) {
        @if(config('fi.baseCurrency') == 'EUR')
            return value.toString().replace(",", ".");
        @endif
            return value.replace(",", ".");
    }

    function systemCurrencyFormat(value) {

        @if(config('fi.baseCurrency') == 'EUR')
            return value.toString().replace(".", ",");
        @endif

            return value;
    }

    function showHideLoaderModal() {
        $('#modal-loader').modal('toggle');
    }

    function itemLookupDetailFills(module_name, fieldName, fieldLabel, data, $this) {
        if (module_name === 'quote') {
            var customFieldType = data.quoteCustomFieldsType;
        }
        if (module_name === 'invoice') {
            var customFieldType = data.customFieldsType;
        }
        if (module_name === 'recurring_invoice') {
            var customFieldType = data.recurringInvoiceCustomFieldsType;
        }

        $this.closest('.table').siblings('.main-table').find("tbody input[name='item_lookup_id']").val(data.custom.item_lookup_id);

        if (customFieldType[fieldName] == 'date') {
            var date = data.customFields[fieldLabel] != null ? moment(data.customFields[fieldLabel]).format(dateFormat) : '';
            $this.val(date);
        } else if (customFieldType[fieldName] == 'datetime') {
            var date_time = data.customFields[fieldLabel] != null ? moment(data.customFields[fieldLabel]).format(dateTimeFormat) : '';
            $this.val(date_time);
        } else if (customFieldType[fieldName] == 'radio') {
            if ($this.data('value') == data.customFields[fieldLabel]) {
                $this.prop("checked", true);
            } else {
                $this.prop("checked", false);
            }
        } else if (customFieldType[fieldName] == 'checkbox') {
            if (data.customFields[fieldLabel] != '') {

                $this.prop("checked", true);
                $this.attr('data-value');

            } else {
                $this.prop("checked", false);
            }
        } else if (customFieldType[fieldName] == 'email') {
            $this.val(data.customFields[fieldLabel]);
        } else if (customFieldType[fieldName] == 'tagselection') {
            $this.closest(".custom-select2").select2().val($.parseJSON(data.customFields[fieldLabel])).trigger('change');
        } else if (customFieldType[fieldName] == 'image') {
            $this.closest('.custom-file').siblings('.custom_img').remove();
            $this.closest('.custom-file').parents('.form-group').children('label').after(data.customFields[fieldLabel]);
            $('.btn-delete-custom-img').click(function () {
                $(this).closest('table').siblings('.table').find('tbody > tr:first-child').find('input[name=data_custom_item_delete]').val('no');
                $(this).closest('.custom_img').html('');
            });
        } else {
            $this.val(data.customFields[fieldLabel]);
        }

    }

    function printPdf(url) {
        var iframe = this._printIframe;
        if (!this._printIframe) {
            iframe = this._printIframe = document.createElement('iframe');
            document.body.appendChild(iframe);

            iframe.style.display = 'none';
            iframe.onload = function () {
                setTimeout(function () {
                    iframe.focus();
                    iframe.contentWindow.print();
                }, 1);
            };
        }
        iframe.src = url;
    }

    $(document).on('click', '.select2-selection--single', function () {
        document.querySelector('.select2-dropdown > .select2-search > .select2-search__field ').focus();
    });


    $(".btn-custom-mail-toggle").on('click', function () {

        var appendId = $(this).data('card-name');

        function runCode() {
            var content = document.getElementById('body').value;
            var iframe = document.getElementById(appendId + '-targetCode');
            iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
            iframe.document.open();
            iframe.document.write(content);
            iframe.document.close();
            return false;
        }

        runCode();

        if (($('.custom-' + appendId + '-sourceCode-display').hasClass('d-block')) === true) {
            $(this).attr('title', '{{trans('fi.code')}}');
            $(this).children().removeClass().addClass('fa fa-code');
            $('.toggle-' + appendId + '-header').html('{{trans('fi.preview')}}');
            $('.custom-' + appendId + '-sourceCode-display').removeClass('d-block').addClass('d-none');
            $('.custom-' + appendId + '-iframe-display').removeClass('d-none').addClass('d-block');
        } else {
            $(this).removeAttr('title');
            $(this).attr('title', '{{trans('fi.preview')}}');
            $(this).children().removeClass().addClass('fa fa-eye');
            $('.toggle-' + appendId + '-header').html('{{trans('fi.code')}}');
            $('.custom-' + appendId + '-sourceCode-display').removeClass('d-none').addClass('d-block');
            $('.custom-' + appendId + '-iframe-display').removeClass('d-block').addClass('d-none');
        }

    });

    $(document).on('click', '.copy-icon-btn', function () {
        var copyText = $(this).closest('tr').find('.item-lookup:first option:selected').text();
        try {
            copyToClipboard(copyText);
            alertify.success('{{trans('fi.copy_text')}}');
        } catch (e) {
            alertify.error(e.message);
        }
    });

    function copyToClipboard(text) {
        var $_document = document;
        var sampleTextarea = $_document.createElement("textarea");
        $_document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text;
        sampleTextarea.select();
        $_document.execCommand("copy");
        $_document.body.removeChild(sampleTextarea);
    }

    function calculateDiscount(discount_type, discount, price) {
        if (discount_type === 'percentage') {
            var returnPrice = (price - ((price * discount) / 100)).toFixed(2);
        } else if (discount_type === 'flat_amount') {
            var returnPrice = (price - discount).toFixed(2);
        } else {
            var returnPrice = price;
        }
        discount_type = discount = price = null;
        return returnPrice;
    }

    function invoiceAndQuoteDiscountItemTotal($_this, discountType) {
        console.log($_this)
        var field = ('discount-type' == discountType);
        var row = $_this.closest('tr');
        var discountVal = row.find('input[name="discount"]').val();
        var discount_type = row.find('select[name="discount_type"]').val();
        if ((typeof discount_type != 'undefined') && (typeof discountVal != 'undefined')) {
            var discount = standardCurrencyFormat(discountVal);
        }
        var price = row.find('input[name="price"]').val();
        var currency = row.find('input[name="price"]').data('currency');
        var quantity = row.find('input[name="quantity"]').val();
        
        var condition = (field == true) ? (discount_type != '') : (discount > 0 && discount_type != '')

        if (condition) {
            if (price > 0 && quantity > 0) {
               
                if (field == true) {
                    $_this.next().removeClass("d-none");
                }
                let totalDiscount = calculateDiscount(discount_type, discount, price);
                let item_subtotal = currencyWithSymbolFormat(currencyFormat((totalDiscount * quantity), currency), currency);
                row.find('.item-subtotal').html(item_subtotal);
            } else {
                if (price <= 0) {                          
                    alertify.error('{{ trans('fi.invalid_price_amount') }}', 5);
                }
                if (quantity <= 0) {
                    alertify.error('{{ trans('fi.invalid_quantity_amount') }}', 5);
                }
            }
        } else {
            if (field == true) {
                $_this.next().addClass("d-none").val('');
            }
           
            if (price > 0 && quantity > 0) {
                let totalDiscount = calculateDiscount(discount_type, discount, (price * 1).toFixed(2));
                let item_subtotal = currencyWithSymbolFormat(currencyFormat((totalDiscount * quantity), currency), currency);
                row.find('.item-subtotal').html(item_subtotal);
            } else {
                row.find('input[name="price"]').prop("readonly", false);             
                if (quantity <= 0) {
                    alertify.error('{{ trans('fi.invalid_quantity_amount') }}', 5);
                }
            }
           
        }
    }

    if ('{{config('time_tracking_enabled')}}' == true) {

        function pad(val) {
            return val > 9 ? val : "0" + val;
        }
    }

    (function ($) {
        $.fn.serializeFormJSON = function () {

            var o = {};
            var a = this.serializeArray();
            $.each(a, function () {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };
    })(jQuery);

</script>