<a href="#" class="nav-link" data-toggle="dropdown" aria-expanded="true">
    <i class="fas fa-bell text-{{$topBarColorText}}">
        @if(count($notifications))
            <span class="badge badge-{{isset($topBarLogoutColorText) ? $topBarLogoutColorText :'danger'}} navbar-badge"
                  style="background: {{isset($topBarLogoutColorText) ? $topBarLogoutColorText : '#dc3545'}}; color : {{($topBarLogoutColorText == '#82FFFF') ? 'black' :'' }}">{{count($notifications)}}</span>
        @endif
    </i>
</a>

<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right notifications-menu"
     style="max-height: 430px;overflow-x: hidden;overflow-y: auto;">
    @if(count($notifications))
        <div class="row">
            <div class="col-md-12">
                <span class="dropdown-item dropdown-header">
                    <a href="javascript:void(0)" data-url="{{route('dashboard.index')}}"
                       title="{{ trans('fi.clear-all') }}"
                       class="btn btn-xs btn-danger float-left clear-all-notifications"><i
                                class="fa fa-trash"></i>
                    </a>
                    {{trans('fi.total_notifications',['total' => count($notifications)])}}
                </span>
            </div>
        </div>

        <div class="dropdown-divider"></div>

        @foreach($notifications as $notification)
            {!! $notification->notification_detail['link'] !!}
            <div class="dropdown-divider"></div>
        @endforeach

    @else
        <span class="dropdown-footer">{{trans('fi.no_notifications')}}</span>
    @endif
</div>