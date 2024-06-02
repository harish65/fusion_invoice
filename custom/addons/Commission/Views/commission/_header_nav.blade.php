@can('commission.view')
    <li>
        <a class="dropdown-item" href="{{ route('invoice.commission.type.index') }}"><i class="fas fa-hand-holding-usd pr-2"></i>
            {{ trans('Commission::lang.commission_types') }}
        </a>
    </li>
@endcan
