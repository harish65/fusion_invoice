@if($mruList->count() > 0)
    <div class="mru-data nav nav-pills nav-sidebar flex-column">
        <li class="nav-header">{{ trans('fi.recently_viewed') }}</li>
        @foreach($mruList as $mru)
            <li class="nav-item small">
                <a href="{{ $mru->url }}" class="nav-link small">
                    <i class="fa {!! $moduleIconMapping[$mru->module] !!}"></i> {{ $mru->title }}
                </a>
            </li>
        @endforeach
    </div>
@endif