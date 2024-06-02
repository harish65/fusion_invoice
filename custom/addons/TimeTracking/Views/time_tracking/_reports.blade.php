<li class="nav-item">
    <a href="{{ route('timeTracking.reports.timesheet') }}" class="small nav-link {{ ($urlSegment1 == 'time_tracking' && $urlSegment2 == 'reports') ? 'active' : '' }}">
        <i class="far fa-circle nav-icon text-info"></i>
        <p>{{ trans('TimeTracking::lang.timesheet') }}</p>
    </a>
</li>