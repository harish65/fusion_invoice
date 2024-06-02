@if ($show)
    @foreach($timeTrackerTasks as $key => $value)
        <a class="btn">
            <div class="card {{ $value['activeTimer'] == true ? 'card-gray':'card-olive' }}">
                <div class="card-header pt-0 pb-0 pl-0 pr-3">
                    <div class="card-tools position-relative">
                        @if ($value['activeTimer'])
                            <button type="button" class="btn btn-tool btn-dashboard-start-timer text-white"
                                    data-timer-id="{{$value['id']}}"
                                    data-task-id="{{ $key }}"
                                    data-project-id="{{$value[ 'project_id']}}">
                                <small> <i class="fa fa-play"></i></small>
                            </button>
                            <button type="button" class="btn btn-tool text-white"
                                    data-widget="chat-pane-toggle" data-card-widget="collapse">
                                <strong>{{$value['name']}}</strong>
                            </button>
                        @else
                            <button type="button" class="btn btn-tool btn-dashboard-stop-timer text-white"
                                    data-timer-id="{{$value['id']}}"
                                    data-task-id="{{ $key }}"
                                    data-project-id="{{$value[ 'project_id']}}">
                                <small> <i class="fa fa-pause"></i></small>
                            </button>
                            <button type="button" class="btn btn-tool text-white"
                                    data-widget="chat-pane-toggle">
                                <strong>{{$value['name']}}</strong>
                            </button>
                        @endif
                        <button type="button"
                                class="btn btn-tool position-absolute btn-dashboard-remove-timer"
                                data-card-widget="remove" data-timer-id="{{$value['id']}}"
                                data-task-id="{{ $key }}"
                                data-project-id="{{$value[ 'project_id']}}">
                            <span class="badge badge-danger navbar-badge bg-danger"><i class="fas fa-times"></i></span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-1">
                    @if ($value['activeTimer'])
                        <small>
                            <strong class="text" style="color:lightgray;">
                                {{$value['formatted_hours']}} {{ trans('TimeTracking::lang.hours') }}
                            </strong>
                        </small>
                    @else
                        <small>
                            <strong class="text-olive">
                                <span id="dashboard_hours_{{$key}}"></span>:
                                <span id="dashboard_minutes_{{$key}}"></span>:
                                <span id="dashboard_seconds_{{$key}}"></span>
                            </strong>
                        </small>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
    <script type="text/javascript">
        @foreach($timeTrackerTasks as $key => $value)
        var date1 = new Date('{{$value['start_at']}}');
        var date2 = new Date('{{$mySqlTime}}');
        var diffSeconds = (date2 - date1) / 1000;
        setTimerInterval('{{$key}}', parseInt('{{$value['seconds']}}') + diffSeconds);
        @endforeach
    </script>
@endif

