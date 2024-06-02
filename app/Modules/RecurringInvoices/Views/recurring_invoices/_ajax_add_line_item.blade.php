<tr id="new-item">
    <td class="handle"><i class="fa fa-sort"></i></td>
    <td colspan="8" class="no-padding">
        <table class="table main-table table-hover table-striped no-padding regular-fields max-content-custom">
            <tr>
                <td class="col-4 copy-to-clipboard-hover">
                    {!! Form::hidden('recurring_invoice_id', $recurring_invoice_id) !!}
                    {!! Form::hidden('id', '') !!}
                    {!! Form::hidden('item_lookup_id',null,['data-item-lookUp-id' => '']) !!}
                    {!! Form::hidden('data_custom_item_delete','yes') !!}
                    {!! itemLookUpsDropDown() !!}
                    <i class="float-right p-2 fa fa-copy copy-icon-btn d-none" title="{{trans('fi.copy')}}"></i>
                    <label class="lbl_item_lookup">
                        <input type="checkbox" class="update_item_lookup"
                               name="save_item_as_lookup"
                               tabindex="999"> {{ trans('fi.save_item_as_lookup') }}
                    </label>
                    {!! Form::textarea('description', null, ['class' => 'description form-control form-control-sm', 'rows' => 3]) !!}
                </td>

                <td class="col-2">{!! Form::text('quantity', null, ['class' => 'form-control form-control-sm']) !!}</td>
                <td class="col-2">{!! Form::text('price', null, ['class' => 'form-control form-control-sm']) !!}</td>
                <td class="col-2">
                    {!! Form::select('tax_rate_id', $taxRates, config('fi.itemTaxRate'), ['class' => 'form-control form-control-sm']) !!}
                    @if(config('fi.numberOfTaxFields') == '2')
                        {!! Form::select('tax_rate_2_id', $taxRates, config('fi.itemTax2Rate'), ['class' => 'form-control form-control-sm']) !!}
                </td>
                @endif
                <td class="col-2"></td>
            </tr>
        </table>

        @if ($recurringInvoiceItemCustomFields)
            @include('custom_fields._custom_fields_unbound_recurring_invoice_items', ['object' => new \FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem(), 'customFields' => $recurringInvoiceItemCustomFields ,'key' => $key ])
        @endif

    </td>
    <td>
        <a class="btn btn-sm btn-danger btn-delete-recurring-invoice-item"
           href="javascript:void(0);"
           title="{{ trans('fi.delete') }}">
            <i class="fa fa-times"></i>
        </a>
    </td>
</tr>