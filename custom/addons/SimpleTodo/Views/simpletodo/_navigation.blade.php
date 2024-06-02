<li class="nav-item">
    <a href="{{ route('simpleTodo.index') }}" class="{{ ($urlSegment1 == 'simpletodo') ? 'nav-link active' : 'nav-link' }}">
        <i class="nav-icon fas fa-tasks"></i>
        <p>{{ trans('SimpleTodo::translations.tasks') }}</p>
    </a>
</li>