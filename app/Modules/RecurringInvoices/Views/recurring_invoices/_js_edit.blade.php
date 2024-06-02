<script type="text/javascript">

    $(function () {

        $("#next_date").datetimepicker({autoclose: true, format: dateFormat});
        $("#stop_date").datetimepicker({autoclose: true, format: dateFormat});
        $('#invoice-tags').select2({tags: true, tokenSeparators: [",", " "]});

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

        $('#btn-copy-recurring-invoice').click(function () {
            $('#modal-placeholder').load('{{ route('recurringInvoiceCopy.create') }}', {
                recurring_invoice_id: '{{ $recurringInvoice->id }}'
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

        $('.btn-save-recurring-invoice').click(function () {
            var items = [];
            var files = [];
            var display_order = 1;
            var custom_fields = {};
            var recurring_invoice_data = {};
            var apply_exchange_rate = $(this).data('apply-exchange-rate');
            var form_data;
            var selectCustomRadioButtonValue = null;
            var selectItemCustomRadioButtonValue = null;
            var $this = $(this);

            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

            $('table tr.item').each(function () {
                let name = ($(this).find('select[name="name"] option:selected').eq(0).text());
                let price = ($(this).find('input[name="price"]').eq(0).val());
                if (name && price) {
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
                        var fieldName = $(this).data("recurring_invoice_items-field-name");
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
                            row["custom"][$(this).data("recurring_invoice_items-field-name")] = $(this).val();
                        }

                    });
                    row['display_order'] = display_order;
                    display_order++;
                    items.push(row);
                }
            });

            $('.custom-file-input,.form-check-input,.custom-form-field').each(function () {
                var fieldName = $(this).data('recurring_invoices-field-name');
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

                    if ('checkbox' === inputType) {
                        custom_fields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                        return true;
                    }
                    custom_fields[fieldName] = $(this).val();
                }
            });
            recurring_invoice_data['recurring_invoice_id'] = $('#recurring_invoice_id').val();
            recurring_invoice_data['custom_module'] = $('#custom_module').val();
            recurring_invoice_data['custom_items_module'] = $('#custom_items_module').val();
            recurring_invoice_data['items'] = items;
            recurring_invoice_data['terms'] = $('#terms').val();
            recurring_invoice_data['footer'] = $('#footer').val();
            recurring_invoice_data['currency_code'] = $('#currency_code').val();
            recurring_invoice_data['exchange_rate'] = $('#exchange_rate').val();
            recurring_invoice_data['custom'] = custom_fields;
            recurring_invoice_data['apply_exchange_rate'] = typeof apply_exchange_rate === 'undefined' ? '' : apply_exchange_rate;
            recurring_invoice_data['template'] = $('#template').val();
            recurring_invoice_data['summary'] = $('#summary').val();
            recurring_invoice_data['discount'] = $('#discount').val();
            recurring_invoice_data['next_date'] = $('#next_date').children().val();
            recurring_invoice_data['stop_date'] = $('#stop_date').children().val();
            recurring_invoice_data['recurring_frequency'] = $('#recurring_frequency').val();
            recurring_invoice_data['recurring_period'] = $('#recurring_period').val();
            recurring_invoice_data['document_number_scheme_id'] = $('#document_number_scheme_id').val();
            recurring_invoice_data['custom_files'] = files;
            recurring_invoice_data['tags'] = $('#invoice-tags').val();

            form_data = objectToFormData(recurring_invoice_data);
            $.ajax({
                url: '{{ route('recurringInvoices.update', [$recurringInvoice->id]) }}',
                method: 'post',
                data: form_data,
                processData: false,
                async: false,
                contentType: false,
                success: function (data) {
                    if (data.error) {
                        alertify.error(data.error, 5);
                    }
                },
            }).done(function () {
                $('#div-recurring-invoice-edit').load('{{ route('recurringInvoiceEdit.refreshEdit', [$recurringInvoice->id]) }}', function () {
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
            helper: fixHelper
        });

        $('.btn-delete-custom-img').click(function () {
            let $this = $(this);
            let ItemCustomId = $(this).closest('.custom-fields-table').siblings('.main-table').data('item-custom-id');
            let url = "{{ route('recurringInvoiceEdit.deleteImage', [$recurringInvoice->id,'field_name' => '']) }}";
            $.post(url + '/' + $(this).data('field-name'), {'item_custom_id': ItemCustomId}).done(function () {
                $this.closest('.custom_img').html('');
            });
        });

        $('.btn-delete-recurring-invoice').click(function () {

            $('#modal-placeholder').load('{!! route('recurring.invoice.delete.modal') !!}', {
                    action: '{{ route('recurringInvoices.delete', [$recurringInvoice->id]) }}',
                    modalName: 'recurring-invoice',
                    isReload: true,
                    returnURL: '{{route('recurringInvoices.index')}}'
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });

        $(document).off('click', '.btn-delete-recurring-invoice-item').on('click', '.btn-delete-recurring-invoice-item', function () {
            var id = $(this).data('item-id');
            if (typeof id === 'undefined') {
                $(this).closest('tr').remove();
                var key = $('#item-table > tbody > tr').length;
                key = (parseInt(key));
                if (key <= 1) {
                    $('.footer-btn-add-item').hide();
                }
            } else {
                $(this).addClass('delete-recurring-invoice-item-active');

                $('#modal-placeholder').load('{!! route('recurring.invoice.item.delete.modal') !!}', {
                        itemId: id,
                        isReload: false,
                        modalName: 'recurring-invoice-item',
                        recurringInvoiceId: '{{ $recurringInvoice->id }}',
                        action: '{{ route('recurringInvoiceItem.delete') }}',
                        refreshURL: '{{ route('recurringInvoiceEdit.refreshTotals') }}',
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

        $(document).on('change', '.description', function () {
            $(this).css('border-color', '#007bff');
        });

        $('#btn-create-live-invoice').click(function () {
            showHideLoaderModal();
            $.post('{{route('recurringInvoices.create.live.invoice')}}', {'id': $(this).data('id')})
                .done(function (response) {
                    showHideLoaderModal();
                    alertify.success(response.message, 5);
                    $('#div-recurring-invoice-edit').load('{{ route('recurringInvoiceEdit.refreshEdit', [$recurringInvoice->id]) }}', function () {
                        var settings = {
                            placeholder: '{{ trans('fi.select-item') }}',
                            allowClear: true,
                            tags: true,
                            selectOnClose: true
                        };
                        $('.item-lookup').select2(settings);
                    });
                }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        });
    });

</script>