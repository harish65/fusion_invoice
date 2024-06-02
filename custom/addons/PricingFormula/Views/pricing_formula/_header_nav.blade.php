@can('item_categories.view')
    <li>
        <a class="dropdown-item" href="{{ route('item.priceFormula.index') }}"><i class="fa fa-superscript pr-2"></i> 
            {{ trans('PricingFormula::lang.price_formula') }}
        </a>
    </li>
@endcan
