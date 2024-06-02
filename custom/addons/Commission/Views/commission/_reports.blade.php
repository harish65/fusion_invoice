@can('commission.view')
    <li class="nav-item">
        <a class="small nav-link {{ $urlSegment2 == 'commission' ? 'active' : '' }}" href="{{ route('invoice.commission.reports.index') }}">
            <i class="far fa-circle nav-icon text-info"></i>
            <p>{{ trans('Commission::lang.invoice_commission') }}</p>
        </a>
    </li>
@endcan
