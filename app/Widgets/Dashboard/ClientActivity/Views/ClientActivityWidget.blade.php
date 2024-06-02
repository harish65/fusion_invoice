@can('recent_client_activity.view')
<div id="client-activity-widget">

    <div class="card card-primary card-outline" id="collapsed-card-client-activity">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-child"></i> {{ trans('fi.recent_client_activity') }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool collapse-toggle-btn" data-widget-name='client-activity' data-card-widget="collapse">
                    <i class="fas fa-minus" id="collapsed-card-icon-client-activity"></i>
                </button>
            </div>
        </div>
        <div class="card-body" id="collapsed-card-display-client-activity">
            <table class="table table-sm table-striped">
                <tbody>
                <tr>
                    <th>{{ trans('fi.date') }}</th>
                    <th>{{ trans('fi.activity') }}</th>
                </tr>
                @foreach ($recentClientActivity as $activity)
                    <tr>
                        <td>{{ $activity->formatted_created_at }}</td>
                        <td>{!! $activity->formatted_activity !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endcan