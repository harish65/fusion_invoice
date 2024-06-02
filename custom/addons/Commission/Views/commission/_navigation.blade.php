@can('commission.view')
<li class="nav-item {{ $urlSegment1 == 'commission' ? 'menu-open' : '' }}">
    <a href="#" class="nav-link ">
        <i class="nav-icon fa fa-hand-holding-usd"></i>
        <p>
            {{ trans('Commission::lang.commissions') }}
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a class="small nav-link {{ $urlSegment2 == 'invoice_commissions' ? 'active' : '' }}" href="{{ route('invoice.commission.index') }}">
                <i class="fas fa-file-invoice nav-icon text-warning"></i>
                <p>{{ trans('Commission::lang.invoice_commission') }}</p>
            </a>
        </li>

        <li class="nav-item">
            <a class="small nav-link {{ $urlSegment2 == 'recurring_commissions' ? 'active' : '' }}" href="{{ route('recurring.invoice.commission.index') }}">
                <i class="fas fa-sync nav-icon text-warning"></i>
                <p>{{ trans('Commission::lang.recurring_invoice_commission') }}</p>
            </a>
        </li>
    </ul>
</li>
@endcan
