@can('time_tracking.view')
<li class="nav-item">
    <a href="{{ route('timeTracking.projects.index', ['status' => 'active']) }}" class="{{ ($urlSegment1 == 'time_tracking' && $urlSegment2 == 'projects') ? 'nav-link active' : 'nav-link' }}">
        <i class="nav-icon fas fa-clock"></i>
        <p>{{ trans('TimeTracking::lang.time_tracking') }}</p>
    </a>
</li>
@endcan