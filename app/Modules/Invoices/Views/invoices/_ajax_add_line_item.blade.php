<tr id="new-item">

    <td class="handle w-0"><i class="fa fa-sort"></i></td>
    <td colspan="{{ $allowLineItemDiscounts == true ? 6 : 5 }}"
        class="no-padding col-12">
        <table class="table main-table table-hover table-borderless no-padding regular-fields max-content-custom">
            <tr>
                <td class=" col-{{$allowLineItemDiscounts == true ? 4 : 5 }} cw-{{$allowLineItemDiscounts == true ? '' : 40 }} copy-to-clipboard-hover">
                    {!! Form::hidden('invoice_id', $invoice) !!}
                    {!! Form::hidden('id', '') !!}
                    {!! Form::hidden('item_lookup_id',null,['data-item-lookUp-id' => '']) !!}
                    {!! Form::hidden('data_custom_item_delete','yes') !!}
                    {!! itemLookUpsDropDown() !!}
                    <i class="float-right mr-1 pr-2 far fa-copy copy-icon-btn d-none" title="{{trans('fi.copy')}}"></i>
                    <label class="lbl_item_lookup">
                        <input type="checkbox" class="update_item_lookup" name="save_item_as_lookup" tabindex="999">
                        {{ trans('fi.save_item_as_lookup') }}
                    </label>

                    {!! Form::textarea('description', null, ['class' => 'description form-control form-control-sm mt-1', 'rows' => 3, 'placeholder' => trans('fi.description')]) !!}
                </td>
                <td class="col-1 cw-{{$allowLineItemDiscounts == true ? '' : 8 }}">{!! Form::text('quantity', null, ['class' => 'form-control form-control-sm quantity', 'data-field'=>'quantity', 'placeholder' => trans('fi.quantity')]) !!}</td>
                <td class="col-2 cw-{{$allowLineItemDiscounts == true ? '' : 16 }}">{!! Form::text('price', null, ['class' => 'form-control form-control-sm price', 'data-currency'=> $currencyCode, 'data-field'=>'price', 'placeholder' => trans('fi.price')]) !!}</td>
                @if($allowLineItemDiscounts == true)
                    <td class="col-2 ">
                        <div class="row">
                            {!! Form::select('discount_type', $discountTypes, '', ['class' => 'form-control form-control-sm discount-type col-5 ml-1', 'data-field'=>'discount-type']) !!}
                            {!! Form::text('discount', null, ['class' => 'form-control form-control-sm discount col-6 d-none ml-1', 'data-field'=>'discount', 'placeholder' => trans('fi.discount-amount')]) !!}
                        </div>
                    </td>
                @endif
                <td class="col-{{ $allowLineItemDiscounts == true ? 1 : 2 }} cw-{{$allowLineItemDiscounts == true ? '' : 8 }}">
                    {!! Form::select('tax_rate_id', $taxRates, config('fi.itemTaxRate'), ['class' => 'form-control form-control-sm']) !!}
                    @if(config('fi.numberOfTaxFields') == '2')
                        {!! Form::select('tax_rate_2_id', $taxRates, config('fi.itemTax2Rate'), ['class' => 'form-control form-control-sm mt-1']) !!}
                    @endif
                </td>
                <td class="col-2 item-subtotal cw-{{$allowLineItemDiscounts == true ? '' : 16 }}"></td>
            </tr>
        </table>
        @if ($invoiceItemCustomFields)
            @include('custom_fields._custom_fields_unbound_item_invoice', ['object' => new \FI\Modules\Invoices\Models\InvoiceItem(), 'customFields' => $invoiceItemCustomFields , 'key' => $key ])
        @endif
    </td>
    <td>
        <a class="btn btn-sm btn-danger btn-delete-invoice-item "
           href="javascript:void(0);" title="{{ trans('fi.delete') }}">
            <i class="fa fa-times"></i>
        </a>
    </td>
</tr>