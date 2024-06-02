<script type="text/javascript">
    $(function () {
        var descriptionSelectUnselect = '';
        var rowSelectUnselect = '';

        select2ItemSelect();
        //select2 init
        select2Init();

        $('.item-lookup').on("select2:select", function (e) {
            rowSelectUnselect = $(this).closest('tr');
            descriptionSelectUnselect = rowSelectUnselect.find('textarea[name="description"]').val();
        });

        function select2Init() {

            // Define the select settings
            var settings = {
                placeholder: '{{ trans('fi.select-item') }}',
                allowClear: true,
                tags: true,
                selectOnClose: true,
            };

            // Make all existing items select
            $('.item-lookup').select2(settings);
        }

        // Sets up .item-lookup to populate proper fields when item is selected
        function select2ItemSelect() {
            $(document).on('select2:select', '.item-lookup', function (e) {
                if (typeof e.params.data.element !== 'undefined') {
                    var row = $(this).closest('tr');
                    var custom_fields = {};
                    var item_lookup_data = {};
                    var moduleName = $(this).closest('.main-table').parents('.table').data('module-name');

                    row.closest('table').siblings('table.custom-fields-table').find('.custom-form-field,.custom-file-input,.form-check-input').each(function () {
                        custom_fields[$(this).data('label').toLowerCase()] = $(this).val();
                    });

                    item_lookup_data['id'] = $(this).val();
                    item_lookup_data['quantity'] = null;
                    item_lookup_data['currency_code'] = (($('#currency_code').length > 0) && ($('#currency_code').val())) ? $('#currency_code').val() : '';
                    item_lookup_data['custom'] = custom_fields;
                    item_lookup_data['moduleName'] = moduleName;
                    item_lookup_data['description'] = row.find('textarea[name="description"]').val();

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
                            row.find('textarea[name="description"]').css('border-color', '');
                            row.find('input[name="quantity"]').val(data.quantity);
                            row.find('input[name="price"]').val(data.price);
                            row.find('input[name="price"]').attr('data-value', data.original_price);
                            row.find('input[name="price"]').attr('data-currency', data.currency.code);
                            if (data.formula_id != null && data.formula_applied == true) {
                                row.find('input[name="price"]').prop("readonly", true);
                            }
                            row.find('select[name="tax_rate_id"]').val(data.tax_rate_id == -1 ? '{{ config('fi.itemTaxRate') }}' : data.tax_rate_id);
                            row.find('select[name="tax_rate_2_id"]').val(data.tax_rate_2_id == -1 ? '{{ config('fi.itemTax2Rate') }}' : data.tax_rate_2_id);
                            row.find('.lbl_item_lookup').hide();
                            if ('customFields' in data && data.formula_applied == false) {

                                row.closest('table').siblings('table.custom-fields-table').find('.custom-form-field,.custom-file-input,.form-check-input').each(function (index) {
                                    let fieldLabel = $(this).data('label').toLowerCase();
                                    let module_name = $('#item-table').attr('data-module-name');
                                    let fieldName = $(this).data(module_name + '_items-field-name');

                                    if (fieldLabel in data.customFields) {
                                        itemLookupDetailFills(module_name, fieldName, fieldLabel, data, $(this))
                                    }
                                });
                            }
                        }
                    });
                } else {
                    var row = $(this).closest('tr');
                    row.find('textarea[name="description"]').val((descriptionSelectUnselect) ? descriptionSelectUnselect : (row.find('textarea[name="description"]').val()));
                    row.find('input[name="quantity"]').val(1);
                    row.find('input[name="price"]').val('');
                    row.find('select[name="tax_rate_id"]').val('{{ config('fi.itemTaxRate') != '' ? config('fi.itemTaxRate') : 0 }}');
                    row.find('select[name="tax_rate_2_id"]').val('{{ config('fi.itemTax2Rate') != '' ? config('fi.itemTax2Rate') : 0 }}');
                    row.find('.lbl_item_lookup').show();
                }
            });
        }

        // Clones a new item row
        function cloneItemRow(initialLoad) {
            var module_name = $('#item-table').attr('data-module-name');
            var module_id = $('#item-table').attr('data-id');
            var key = $('#item-table > tbody > tr').length;
            key = (parseInt(key) + parseInt(1));

            if (module_name != 'undefined' && module_name != '') {
                if (module_name === 'invoice') {
                    var routes = "{{route('invoice.add.new.lineItem')}}";
                }
                if (module_name === 'recurring_invoice') {
                    var routes = "{{route('recurring.invoice.add.new.lineItem')}}";
                }
                if (module_name === 'quote') {
                    var routes = "{{route('quotes.add.new.lineItem')}}";
                }
            }

            $.ajax({
                url: routes,
                data: {id: module_id, key: key},
                method: 'POST',
                success: function (dataTemplate) {
                    $('#item-table > tbody').append(dataTemplate);
                    var row = $('#item-table > tbody > tr:last');
                    row.removeAttr('id').addClass('item');
                    row.find('select[name="name"]').addClass('item-lookup');

                    if (initialLoad == true) {
                        row.find('.btn-danger').remove();
                    }
                    if (key >= 2) {
                        $('.footer-btn-add-item').show();
                    }
                    select2Init();
                    $('.custom-select2').select2({tags: true, tokenSeparators: [",", " "]});
                }
            });
        }

        $(document).on('click', '#btn-add-item', function () {
            cloneItemRow(false);
        });

        // Add a new item row if no items currently exist
        @if (!$itemCount)
        cloneItemRow(true);
        @endif

    });

</script>