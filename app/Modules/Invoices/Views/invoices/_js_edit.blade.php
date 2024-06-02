<script type="text/javascript">

    $(function () {

        @if($noChange == 1)
        alertify.error('{{trans('fi.there_is_some_error')}}', 5);
        @endif

        $("#invoice_date").datetimepicker({autoclose: true, format: dateFormat});

        $("#due_at").datetimepicker({autoclose: true, format: dateFormat});

        $('#invoice-tags').select2({tags: true, tokenSeparators: [",", " "]});

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

        $('.empty-invoice-delete').click(function () {
            $.post('{{route('invoices.empty.invoice.delete',['id' =>  $invoice->id])}}');
        });

        $('#btn-copy-invoice').click(function () {
            $('#modal-placeholder').load('{{ route('invoiceCopy.create') }}', {
                invoice_id: '{{ $invoice->id }}'
            });
        });

        $('#btn-copy-recurring-invoice').click(function () {
            $('#modal-placeholder').load('{{ route('invoiceToRecurringInvoiceCopy.create') }}', {
                invoice_id: '{{ $invoice->id }}'
            });
        });

        $('#btn-invoice-view-timeline').click(function () {
            $('#modal-placeholder').load('{{ route('invoice.showTimeLine')}}', {
                invoice_id: '{{ $invoice->id }}'
            });
        });

        $('.send-overdue-reminder').click(function () {
            var $_this = $(this);
            $.ajax({
                url: $_this.data('action'),
                method: 'get',
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function (data) {
                    showHideLoaderModal();

                    if (data.success) {
                        alertify.success(data.message, 5);
                    } else {
                        alertify.error(data.message, 5);
                    }
                },
                error: function () {
                    showHideLoaderModal();
                    alertify.error('{{ trans('fi.error_sending_reminder') }}', 5);
                }
            });

        });

        $('.send-upcoming-notice').click(function () {

            var $_this = $(this);
            $.ajax({
                url: $_this.data('action'),
                method: 'get',
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function (data) {
                    showHideLoaderModal();

                    if (data.success) {
                        alertify.success(data.message, 5);
                    } else {
                        alertify.error(data.message, 5);
                    }
                },
                error: function () {
                    showHideLoaderModal();
                    alertify.error('{{ trans('fi.error_sending_reminder') }}', 5);
                }
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
                $('#currency_code, #exchange_rate').addClass('bg-secondary');
            } else {
                $('#currency_code, #exchange_rate').removeClass('bg-secondary');
            }

            $.post('{{ route('currencies.getExchangeRate') }}', {
                currency_code: $('#currency_code').val()
            }, function (data) {
                $('#exchange_rate').val(data);
            });
        }

        $('.btn-save-invoice').click(function () {
            var items = [];
            var files = [];
            var display_order = 1;
            var custom_fields = {};
            var invoice_data = {};
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
                if (qty && name && price) {
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
                        var fieldName = $(this).data("invoice_items-field-name");
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

                            row["custom"][$(this).data("invoice_items-field-name")] = $(this).val();
                        }
                    });

                    row['display_order'] = display_order;
                    display_order++;
                    items.push(row);
                }
            });

            $('.custom-file-input,.form-check-input,.custom-form-field').each(function () {
                var fieldName = $(this).data('invoices-field-name');
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

            invoice_data['number'] = $('#number').val();
            invoice_data['invoice_id'] = $('#invoice_id').val();
            invoice_data['custom_module'] = $('#custom_module').val();
            invoice_data['custom_items_module'] = $('#custom_items_module').val();
            invoice_data['invoice_date'] = $('#invoice_date').children().val();
            invoice_data['due_at'] = $('#due_at').children().val();
            invoice_data['status'] = $('#status').val();
            invoice_data['items'] = items;
            invoice_data['terms'] = $('#terms').val();
            invoice_data['footer'] = $('#footer').val();
            invoice_data['currency_code'] = $('#currency_code').val();
            invoice_data['exchange_rate'] = $('#exchange_rate').val();
            invoice_data['custom'] = custom_fields;
            invoice_data['apply_exchange_rate'] = typeof apply_exchange_rate === 'undefined' ? '' : apply_exchange_rate;
            invoice_data['template'] = $('#template').val();
            invoice_data['summary'] = $('#summary').val();
            invoice_data['discount'] = $('#discount').val();
            invoice_data['custom_files'] = files;
            invoice_data['tags'] = $('#invoice-tags').val();
            invoice_data['online_payment_processing_fee'] = $('#online_payment_processing_fee').val();

            form_data = objectToFormData(invoice_data);

            $.ajax({
                url: '{{ route('invoices.update', [$invoice->id]) }}',
                method: 'post',
                data: form_data,
                async: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.error) {
                        alertify.error(data.error, 5);
                    } else {
                        alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                    }
                },
            }).done(function () {
                $('#div-invoice-edit').load('{{ route('invoiceEdit.refreshEdit', [$invoice->id]) }}', function () {
                    var settings = {
                        placeholder: '{{ trans('fi.select-item') }}',
                        allowClear: true,
                        tags: true,
                        selectOnClose: true
                    };

                    // Make all existing items select
                    $('.item-lookup').select2(settings);

                    var url = window.location.href;
                    var params = new URLSearchParams(url.split("?")[1]);
                    if (params.has("overlay")) {
                        var newUrl = url.split("?")[0];
                        window.location.href = newUrl;
                    }
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
            let url = "{{ route('invoiceEdit.deleteImage', [$invoice->id,'field_name' => '']) }}";
            $.post(url + '/' + $(this).data('field-name'), {'item_custom_id': ItemCustomId}).done(function () {
                $this.closest('.custom_img').html('');
            });
        });

        $(document).off('click', '.btn-delete-invoice-item').on('click', '.btn-delete-invoice-item', function () {
            var id = $(this).data('item-id');
            if (typeof id === 'undefined') {
                $(this).closest('tr.item').remove();
                var key = $('#item-table > tbody > tr').length;
                key = (parseInt(key));
                if (key <= 1) {
                    $('.footer-btn-add-item').hide();
                }
            } else {
                $(this).addClass('delete-invoices-item-active');

                $('#modal-placeholder').load('{!! route('invoices.item.delete.modal') !!}', {
                        itemId: id,
                        isReload: false,
                        modalName: 'invoices-item',
                        invoiceId: '{{ $invoice->id }}',
                        action: '{{ route('invoiceItem.delete') }}',
                        refreshURL: '{{ route('invoiceEdit.refreshTotals') }}',
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

        $('.btn-delete-invoice').click(function () {

            $('#modal-placeholder').load('{!! route('invoices.delete.modal') !!}', {
                    action: '{{ route('invoices.delete', [$invoice->id]) }}',
                    modalName: 'invoices',
                    isReload: true,
                    returnURL: '{{route('invoices.index')}}'
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });

        $('.apply-credit-memo').click(function () {
            var url = '{{ route("payments.prepareInvoiceSettlementWithCreditMemo", ":invoice") }}';
            url = url.replace(':invoice', $(this).data('invoice-id'));
            var redirect_url = $(this).data('redirect-to');
            $('#modal-placeholder').load(url, {
                redirect_to: redirect_url
            });
        });

        $('.apply-pre-payment').click(function () {
            var url = '{{ route("payments.prepareInvoiceSettlementWithPrePayment", ":invoice") }}';
            url = url.replace(':invoice', $(this).data('invoice-id'));
            var redirect_url = $(this).data('redirect-to');
            $('#modal-placeholder').load(url, {
                redirect_to: redirect_url
            });
        });

        $('.apply-to-invoices').click(function () {
            var url = '{{ route("payments.prepareCreditApplication", ":creditMemo") }}';
            url = url.replace(':creditMemo', $(this).data('invoice-id'));
            var redirect_url = $(this).data('redirect-to');
            $('#modal-placeholder').load(url, {
                redirect_to: redirect_url
            });
        });

        $('#btn-un-mail-invoice,#btn-mail-invoice,#btn-invoice-status-change-to-draft').click(function () {
            $.post($(this).data('action')).done(function (response) {
                if (response.success == true) {
                    $('#div-invoice-edit').load('{{ route('invoiceEdit.refreshEdit', [$invoice->id]) }}', function () {
                        alertify.success(response.message, 5);
                        var settings = {
                            placeholder: '{{ trans('fi.select-item') }}',
                            allowClear: true,
                            tags: true,
                            selectOnClose: true
                        };

                        // Make all existing items select
                        $('.item-lookup').select2(settings);
                    });
                } else {
                    alertify.error(response.message, 5);
                }
            }).fail(function (response) {
                if (response.status == 401) {
                    alertify.error($.parseJSON(response.responseText).message);
                } else {
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
        });

        $('#btn-print-invoice').click(function () {
            $.get($(this).data('action')).done(function (response) {
                window.open(response).print();
            });
        });

        $(document).off('keyup', 'input[data-label="Days"]').on('keyup', 'input[data-label="Days"]', function () {
            var row = $(this).closest('table').parent().find('.regular-fields').children().children();
            var item_lookup_id = row.find('.item-lookup').val();
            var item_lookup_text = row.find('.item-lookup').find(":selected").text();
            var is_tag = row.find('.item-lookup option:selected').attr('data-select2-tag');

            if (!isNaN(item_lookup_id) && item_lookup_id != '' && is_tag != 'true' && item_lookup_id != item_lookup_text) {
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

        $(document).unbind("change").on('keyup', "input[name='quantity']", function () {
            var row = $(this).closest('tr');
            var item_lookup_id = row.find('.item-lookup').val();
            var item_lookup_text = row.find('.item-lookup').find(":selected").text();
            var is_tag = row.find('.item-lookup option:selected').attr('data-select2-tag');

            if (!isNaN(item_lookup_id) && item_lookup_id != '' && is_tag != 'true' && item_lookup_id != item_lookup_text) {
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

        $('body').on('change', '.discount-type', function () {
            invoiceAndQuoteDiscountItemTotal($(this), 'discount-type');
        });

        $('body').on('change', '.discount,.quantity,.price', function () {
            invoiceAndQuoteDiscountItemTotal($(this), 'other');
        });

        $(document).on('change', '.description', function () {
            $(this).css('border-color', '#007bff');
        });

        $('.change-summary-and-tags').change(function () {

            $('.btn-summary-and-tags').removeClass('d-none');
            $('.btn-summary-and-tags').addClass('d-block');

        });

        $(document).off('click', '.btn-summary-and-tags').on('click', '.btn-summary-and-tags', function () {

            $.post($(this).data('action'), {summary: $('#summary').val(), tags: $('#invoice-tags').val()})
                .done(function (response) {
                    if (response.success == true) {
                        $('#div-invoice-edit').load('{{ route('invoiceEdit.refreshEdit', [$invoice->id]) }}', function () {
                            alertify.success(response.message, 5);
                            var settings = {
                                placeholder: '{{ trans('fi.select-item') }}',
                                allowClear: true,
                                tags: true,
                                selectOnClose: true
                            };

                            // Make all existing items select
                            $('.item-lookup').select2(settings);
                        });
                    } else {
                        alertify.error(response.message, 5);
                    }
                }).fail(function (response) {
                if (response.status == 401) {
                    alertify.error($.parseJSON(response.responseText).message);
                } else {
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
        });

        $('.btn-edit-invoice-sent-and-paid').click(function () {

            var $_document = $(document).children();

            $_document.find('.paid-overlay-true').removeClass('paid-overlay-true');
            $_document.find('.paid-overlay').removeClass('paid-overlay');
            $_document.find('#terms').attr("disabled", false);
            $_document.find('#footer').attr("disabled", false);
            $_document.find('#btn-change-company-profile').attr("disabled", false);
            $_document.find('#btn-change-client').attr("disabled", false);
            $_document.find('#btn-edit-client').attr("disabled", false);

            $.post('{{ route('allow.editing.invoices.in.status') }}', {
                status: $(this).data('status'),
                invoice_id: '{{ $invoice->id }}'
            }).done(function (response) {
                if (response.success == true) {
                    alertify.success(response.message);
                } else {
                    alertify.error(response.message);
                }
            }).fail(function (xhr) {
                let errors = JSON.parse(xhr.responseText).errors;
                $.each(errors, function (name, data) {
                    alertify.error(data[0], 5);
                });
            });

        });

        function checkOverlay() {

            @if((config('fi.allowEditInvoiceStatus') == 'draft') && ($invoice->status == 'sent' || $invoice->paid_status == true))
                return false;
            @endif

            @if($overlay == false)

            var $_document = $(document).children();

            $_document.find('.paid-overlay-true').removeClass('paid-overlay-true');
            $_document.find('.paid-overlay').removeClass('paid-overlay');
            $_document.find('#terms').attr("disabled", false);
            $_document.find('#footer').attr("disabled", false);
            $_document.find('#btn-change-company-profile').attr("disabled", false);
            $_document.find('#btn-change-client').attr("disabled", false);
            $_document.find('#btn-edit-client').attr("disabled", false);
            $_document.find('.btn-delete-invoice-item').removeClass("disabled");
            $_document.find('#btn-add-item').attr("disabled", false);
            $_document.find('#currency_code').attr("disabled", false);
            $_document.find('#exchange_rate').attr("disabled", false);
            $_document.find('.overlay-button').removeClass('d-none');

            @endif
        }

        checkOverlay();

    });

</script>