<script type="text/javascript">

    $(function () {

        $("#quote_date").datetimepicker({autoclose: true, format: dateFormat});
        $("#expires_at").datetimepicker({autoclose: true, format: dateFormat});

        $(document).on("mouseenter", ".copy-text", function () {
            $(this).fadeTo(1, 1);
        });

        $(document).on("mouseleave", ".copy-text", function () {
            $(this).fadeTo(1, 0);
        });

        $(document).on('click', '.copy-text', function () {
            let val = $(this).closest('tr').find('.item-lookup:first option:selected').val();
            if (val) {
                let item_text = $(this).closest('tr').find('.item-lookup:first option:selected').text();
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(item_text).select();
                document.execCommand("copy");
                $temp.remove();
                alertify.success("{{trans('fi.text_copied_clipboard')}}", 3);
            }
        });

        $(document).on('change', '.item-lookup', function () {
            var row = $(this).closest('tr');
            row.find('.lbl_item_lookup > .update_item_lookup').prop("checked", false);
        });

        $(document).on('change', 'textarea[name="description"],input[name="price"],select[name="tax_rate_id"],select[name="tax_rate_2_id"]', function () {
            var row = $(this).closest('tr');
            row.find('.lbl_item_lookup').show();
            if (!row.find('.lbl_item_lookup > .update_item_lookup').prop("checked") && typeof row.find('.item-lookup option:selected').val() != "undefined" && row.find('.item-lookup option:selected').val() != row.find('.item-lookup option:selected').text()) {
                row.find('.lbl_item_lookup').show().html('<input type="checkbox" class="update_item_lookup" name="save_item_as_lookup" tabindex="999"> {{ trans('fi.update_item_as_lookup') }}');
            }
        });

        $('#btn-copy-quote').click(function () {
            $('#modal-placeholder').load('{{ route('quoteCopy.create') }}', {
                quote_id: '{{ $quote->id }}'
            });
        });

        $('#btn-quote-to-invoice').click(function () {
            $('#modal-placeholder').load('{{ route('quoteToInvoice.create') }}', {
                quote_id: '{{ $quote->id }}',
                client_id: '{{ $quote->client_id }}'
            });
        });

        $('#btn-update-exchange-rate').click(function () {
            $('.update-exchange').addClass('fa-spin')
            setTimeout(function () {
                $('.update-exchange').removeClass('fa-spin')
            }, 1500);
            updateExchangeRate();
        });

        $('#currency_code').change(function () {
            updateExchangeRate();
        });

        function updateExchangeRate() {

            if ($('#currency_code').val() != '{{ config('fi.baseCurrency') }}') {
                $('#currency_code, #exchange_rate').css('background', '#fff8dc');
            } else {
                $('#currency_code, #exchange_rate').css('background', 'none');
            }

            $.post('{{ route('currencies.getExchangeRate') }}', {
                currency_code: $('#currency_code').val()
            }, function (data) {
                $('#exchange_rate').val(data);
            });
        }

        $('.btn-save-quote').click(function () {
            var items = [];
            var files = [];
            var display_order = 1;
            var quote_data = {};
            var custom_fields = {};
            var apply_exchange_rate = $(this).data('apply-exchange-rate');
            var form_data;
            var selectCustomRadioButtonValue = null;
            var selectItemCustomRadioButtonValue = null;
            var $this = $(this);

            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);
           
           
            $('table tr.item').each(function () {
                let qty = ($(this).find('input[name="quantity"]').eq(0).val());
                let name = ($(this).find('select[name="name"] option:selected').eq(0).text());
                let price = ($(this).find('input[name="price"]').eq(0).val());
               
                if (qty && name && price > 0) {
                    var row = {};
                    $(this).find('.regular-fields').find('text,input,select,textarea').each(function () {
                        if ($(this).attr('name') !== undefined) {
                            if ($(this).is(':checkbox')) {
                                if ($(this).is(':checked')) {
                                    row[$(this).attr('name')] = 1;
                                } else {
                                    row[$(this).attr('name')] = 0;
                                }
                            } else {
                                if ($(this).attr('name') == 'name') {
                                    row[$(this).attr('name')] = name;
                                } else {
                                    row[$(this).attr('name')] = $(this).val();
                                }
                            }
                        }
                    });

                    if ($(this).find('.custom-fields-table').length) {
                        row["custom"] = {};
                    }
                    $(this).find('.custom-fields-table').find('text,input,select,textarea').each(function () {

                        var fieldName = $(this).data("quote_items-field-name");
                        var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                        if (fieldName !== undefined) {
                            if ('file' === inputType) {
                                row["custom"][fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];
                                return true;
                            }
                            if ('select' === inputType) {
                                if ($(this).find('option:selected').length == 0) {
                                    row["custom"][fieldName] = '';
                                    return true;
                                }
                            }
                            if ('checkbox' === inputType) {
                                row["custom"][fieldName] = ($(this).is(":checked")) ? 1 : 0;
                                return true;
                            }
                            if ('radio' === inputType) {
                                if ($(this).prop('checked') == true) {
                                    row["custom"][fieldName] = $(this).val();
                                    selectItemCustomRadioButtonValue = $(this).val();
                                }
                                if ($(this).prop('checked') == false && selectItemCustomRadioButtonValue == null) {
                                    row["custom"][fieldName] = 'null';
                                }
                                if ($(this).prop('checked') == false) {
                                    selectItemCustomRadioButtonValue = null;
                                }
                                return row["custom"][fieldName];
                            }
                            row["custom"][$(this).data("quote_items-field-name")] = $(this).val();
                        }
                    });
                    row['display_order'] = display_order;
                    display_order++;
                    items.push(row);
                }else if (price < 1){
                    alertify.error('{{ trans('fi.invalid_price_amount') }}', 5);
                }

               
            });

            $('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                var fieldName = $(this).data('quotes-field-name');
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

                    custom_fields[$(this).data('quotes-field-name')] = $(this).val();
                }
            });

            quote_data['number'] = $('#number').val();
            quote_data['quote_id'] = $('#quotes_id').val();
            quote_data['custom_module'] = $('#custom_module').val();
            quote_data['custom_items_module'] = $('#custom_items_module').val();
            quote_data['quote_date'] = $('#quote_date').children().val();
            quote_data['expires_at'] = $('#expires_at').children().val();
            quote_data['status'] = $('#status').val();
            quote_data['items'] = items;
            quote_data['terms'] = $('#terms').val();
            quote_data['footer'] = $('#footer').val();
            quote_data['currency_code'] = $('#currency_code').val();
            quote_data['exchange_rate'] = $('#exchange_rate').val();
            quote_data['custom'] = custom_fields;
            quote_data['apply_exchange_rate'] = typeof apply_exchange_rate === 'undefined' ? '' : apply_exchange_rate;
            quote_data['template'] = $('#template').val();
            quote_data['summary'] = $('#summary').val();
            quote_data['discount'] = $('#discount').val();
            quote_data['custom_files'] = files;

            form_data = objectToFormData(quote_data);
            $.ajax({
                url: '{{ route('quotes.update', [$quote->id]) }}',
                method: 'post',
                data: form_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.error) {
                        alertify.error(data.error, 5);
                    }
                },
            }).done(function () {

                $('#div-quote-edit').load('{{ route('quoteEdit.refreshEdit', [$quote->id]) }}', function () {
                    alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);

                    var settings = {
                        placeholder: '{{ trans('fi.select-item') }}',
                        allowClear: true,
                        tags: true,
                        selectOnClose: true
                    };

                    // Make all existing items select
                    $('.item-lookup').select2(settings);
                });
                $this.html($this.data('original-text')).attr("disabled", false);
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                $.each($.parseJSON(response.responseText).errors, function (id, message) {
                    alertify.error(message[0], 5);
                });
            });

        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item-table tbody").sortable({
            helper: fixHelper,
            handle: ".handle"
        });

        $('.btn-delete-custom-img').click(function () {
            let $this = $(this);
            let ItemCustomId = $(this).closest('.custom-fields-table').siblings('.main-table').data('item-custom-id');
            let url = "{{ route('quoteEdit.deleteImage', [$quote->id,'field_name' => '']) }}";
            $.post(url + '/' + $(this).data('field-name'), {'item_custom_id': ItemCustomId}).done(function () {
                $this.closest('.custom_img').html('');
            });
        });

        $(document).off('click', '.btn-delete-quote-item').on('click', '.btn-delete-quote-item', function () {
            var id = $(this).data('item-id');
            if (typeof id === 'undefined') {
                $(this).closest('tr').remove();
                var key = $('#item-table > tbody > tr').length;
                key = (parseInt(key));
                if (key <= 1) {
                    $('.footer-btn-add-item').hide();
                }
            } else {
                $(this).addClass('delete-quotes-item-active');

                $('#modal-placeholder').load('{!! route('quotes.item.delete.modal') !!}', {
                        itemId: id,
                        isReload: false,
                        modalName: 'quotes-item',
                        quoteId: '{{ $quote->id }}',
                        action: '{{ route('quoteItem.delete') }}',
                        refreshURL: '{{ route('quoteEdit.refreshTotals') }}',
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

        $('.btn-delete-quote').click(function () {

            $('#modal-placeholder').load('{!! route('quotes.delete.modal') !!}', {
                    action: '{{ route('quotes.delete', [$quote->id]) }}',
                    modalName: 'quotes',
                    isReload: true,
                    returnURL: '{{route('quotes.index')}}'
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );

        });

        $('#btn-print-quote').click(function () {
            $.get($(this).data('action')).done(function (response) {
                window.open(response).print();
            });
        });

        $('body').on('change', '.discount-type', function () {
            invoiceAndQuoteDiscountItemTotal($(this), 'discount-type');
        });

        $('body').on('change', '.discount,.quantity,.price', function () {
           
            
            invoiceAndQuoteDiscountItemTotal($(this), 'other');
        });
       

        $(document).unbind("change").on('change', 'input[name="quantity"]', function (e) {

            var row = $(this).closest('tr');
            var item_lookup_id = row.find('.item-lookup').val();
            var item_lookup_text = row.find('.item-lookup').find(":selected").text();

            if (!isNaN(item_lookup_id) && item_lookup_id != '' && item_lookup_id != item_lookup_text) {
                var custom_fields = {};
                var item_lookup_data = {};
                row.closest('table').siblings('table.custom-fields-table').find('.custom-form-field').each(function () {
                    custom_fields[$(this).data('label').toLowerCase()] = $(this).val();
                });
                item_lookup_data['id'] = item_lookup_id;
                item_lookup_data['quantity'] = row.find('input[name="quantity"]').val();
                item_lookup_data['currency_code'] = (($('#currency_code').length > 0) && ($('#currency_code').val())) ? $('#currency_code').val() : '';
                item_lookup_data['custom'] = custom_fields;

                $.ajax({
                    url: "{{ route('itemLookups.getDetail') }}",
                    method: 'post',
                    data: item_lookup_data,
                    beforeSend: function () {
                        $(".modal-loader").show();
                    },
                    success: function (data) {
                        $(".modal-loader").hide();
                        row.find('input[name="price"]').prop("readonly", false);
                        row.find('textarea[name="description"]').val(data.description);
                        row.find('input[name="price"]').val(data.price);
                        if (data.formula_id != null && data.formula_applied == true) {
                            row.find('input[name="price"]').prop("readonly", true);
                        }
                        row.find('select[name="tax_rate_id"]').val(data.tax_rate_id == -1 ? '{{ config('fi.itemTaxRate') }}' : data.tax_rate_id);
                        row.find('select[name="tax_rate_2_id"]').val(data.tax_rate_2_id == -1 ? '{{ config('fi.itemTax2Rate') }}' : data.tax_rate_2_id);
                        row.find('.lbl_item_lookup').hide();
                        if ('customFields' in data && data.formula_applied == false) {
                            row.closest('table').siblings('table.custom-fields-table').find('.custom-form-field').each(function (index) {
                                let fieldLabel = $(this).data('label').toLowerCase();
                                if (fieldLabel in data.customFields) {
                                    $(this).val(data.customFields[fieldLabel]);
                                }
                            });
                        }
                    }
                });
            }
        });

        $(document).on('change', '.custom-form-field', function () {
            var row = $(this).closest('tr');
            var item_lookup_id = row.closest('table').siblings('table.regular-fields').find('.item-lookup').val();
            var item_lookup_text = row.closest('table').siblings('table.regular-fields').find('.item-lookup').find(":selected").text();

            if (!isNaN(item_lookup_id) && item_lookup_id != '' && item_lookup_id != item_lookup_text) {
                var custom_fields = {};
                var item_lookup_data = {};
                row.find('.custom-form-field').each(function () {
                    custom_fields[$(this).data('label').toLowerCase()] = $(this).val();
                });
                item_lookup_data['id'] = item_lookup_id;
                item_lookup_data['quantity'] = row.closest('table').siblings('table.regular-fields').find('input[name="quantity"]').val();
                item_lookup_data['currency_code'] = (($('#currency_code').length > 0) && ($('#currency_code').val())) ? $('#currency_code').val() : '';
                item_lookup_data['custom'] = custom_fields;

                $.ajax({
                    url: "{{ route('itemLookups.getDetail') }}",
                    method: 'post',
                    data: item_lookup_data,
                    beforeSend: function () {
                        $(".modal-loader").show();
                    },
                    success: function (data) {
                        $(".modal-loader").hide();
                        row.closest('table').siblings('table.regular-fields').find('input[name="price"]').prop("readonly", false);
                        row.closest('table').siblings('table.regular-fields').find('textarea[name="description"]').val(data.description);
                        row.closest('table').siblings('table.regular-fields').find('input[name="price"]').val(data.price);
                        if (data.formula_id != null && data.formula_applied == true) {
                            row.closest('table').siblings('table.regular-fields').find('input[name="price"]').prop("readonly", true);
                        }
                        row.closest('table').siblings('table.regular-fields').find('select[name="tax_rate_id"]').val(data.tax_rate_id == -1 ? '{{ config('fi.itemTaxRate') }}' : data.tax_rate_id);
                        row.closest('table').siblings('table.regular-fields').find('select[name="tax_rate_2_id"]').val(data.tax_rate_2_id == -1 ? '{{ config('fi.itemTax2Rate') }}' : data.tax_rate_2_id);
                        row.closest('table').siblings('table.regular-fields').find('.lbl_item_lookup').hide();
                        if ('customFields' in data && data.formula_applied == false) {
                            row.find('.custom-form-field').each(function () {
                                let fieldLabel = $(this).data('label').toLowerCase();
                                if (fieldLabel in data.customFields) {
                                    $(this).val(data.customFields[fieldLabel]);
                                }
                            });
                        }
                    }
                });
            }
        });

        $(document).on('change', '.description', function () {
            $(this).css('border-color', '#007bff');
        });

    });

</script>