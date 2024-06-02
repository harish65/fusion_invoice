<div class="card card-primary card-outline">
    <div class="card-body">

        <span class="float-left"><strong>{{ trans('TimeTracking::lang.unbilled_hours') }}</strong></span>
        <span class="float-right">{{ $project->unbilled_hours }}</span>

        <div class="clearfix"></div>

        <span class="float-left"><strong>{{ trans('TimeTracking::lang.billed_hours') }}</strong></span>
        <span class="float-right">{{ $project->billed_hours }}</span>

        <div class="clearfix"></div>

        <span class="float-left"><strong>{{ trans('TimeTracking::lang.total_hours') }}</strong></span>
        <span class="float-right">{{ $project->hours }}</span>

        <div class="clearfix"></div>

    </div>
</div>