<tr id="new-item">
    <td class="handle"><i class="fa fa-sort"></i></td>

    <td colspan="{{ $allowLineItemDiscounts == true ? 6 : 5 }}"
        class="no-padding col-12">

        <table class="table main-table table-hover table-borderless no-padding regular-fields max-content-custom">
            <tr>
                <td class="col-4 copy-to-clipboard-hover">
                    {!! Form::hidden('quote_id', $quote_id) !!}
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

                    {!! Form::textarea('description', null, ['class' => 'description form-control form-control-sm mt-1', 'rows' => 3, 'placeholder' => trans('fi.description')]) !!}

                </td>
                <td class="col-{{ $allowLineItemDiscounts == true ? 1 : 2 }}">{!! Form::text('quantity', null,['class' => 'form-control form-control-sm quantity','placeholder' => trans('fi.quantity')]) !!}</td>
                <td class="col-2">{!! Form::text('price', null, ['class' => 'form-control form-control-sm price','data-currency'=> $currencyCode,'placeholder' => trans('fi.price')]) !!}</td>
                @if($allowLineItemDiscounts == true)

                    <td class="col-2">

                        <div class="row">
                            {!! Form::select('discount_type', $discountTypes, '', ['class' => 'form-control form-control-sm discount-type col-5 ml-1']) !!}
                            {!! Form::text('discount', null, ['class' => 'form-control form-control-sm discount col-6 d-none ml-1','placeholder' => trans('fi.discount-amount')]) !!}
                        </div>

                    </td>

                @endif
                <td class="col-{{ $allowLineItemDiscounts == true ? 1 : 2 }}">

                    {!! Form::select('tax_rate_id', $taxRates, config('fi.itemTaxRate'), ['class' => 'form-control form-control-sm']) !!}
                    @if(config('fi.numberOfTaxFields') == '2')
                        {!! Form::select('tax_rate_2_id', $taxRates, config('fi.itemTax2Rate'), ['class' => 'form-control form-control-sm mt-1']) !!}
                    @endif

                </td>
                <td class="col-2 item-subtotal"></td>
            </tr>
        </table>

        @if ($quoteItemCustomFields)
            @include('custom_fields._custom_fields_unbound_quote_items', ['object' => new \FI\Modules\Quotes\Models\QuoteItem(), 'customFields' => $quoteItemCustomFields ,'key' => $key ])
        @endif

    </td>
    <td>
        <a class="btn btn-sm btn-danger btn-delete-quote-item"
           href="javascript:void(0);" title="{{ trans('fi.delete') }}">
            <i class="fa fa-times"></i>
        </a>
    </td>
</tr>