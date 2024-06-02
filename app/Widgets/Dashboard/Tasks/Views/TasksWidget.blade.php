@include('layouts._select2')
@include('tasks.widget._js_widget')
@include('layouts._formdata')
<div class="card card-primary card-outline" id="collapsed-card-tasks">
    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-tasks mr-1"></i>
            {{ trans('fi.task_list') }}
        </h3>

        <div class="card-tools pull-right">
            <button type="button" class="btn btn-tool collapse-toggle-btn" data-widget-name='tasks'
                    data-card-widget="collapse">
                <i class="fas fa-minus" id="collapsed-card-icon-tasks"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="collapsed-card-display-tasks">
        <div class="row pl-2 flex-row-reverse mb-2">
            <div class="float-right">

                {!! Form::open(['method' => 'GET', 'url' => route('task.widget.list'), 'id' => 'tasks-filter-form']) !!}
                <ul class="nav nav-pills ml-auto">
                    <input type="hidden" id="cookie_date_from" value="{{\Cookie::get('dataFrom')}}">
                    <input type="hidden" id="cookie_date_to" value="{{\Cookie::get('dataTo')}}">
                    <input type="hidden" id="cookie_status" value="{{\Cookie::get('status')}}">
                    <input type="hidden" id="cookie_assignee" value="{{\Cookie::get('assignee')}}">
                    <input type="hidden" id="cookie_search" value="{{\Cookie::get('search')}}">

                    <li class="nav-item mb-1 mr-1">
                        <div class="input-group input-group-sm mt-1">
                        <span class="input-group-prepend">
                            <button type="button" data-toggle="modal" data-target="#modal-search-config"
                                    id="search-config-btn" class="btn btn-sm btn-default"><i
                                        class="fa fa-ellipsis-v"></i>
                            </button>
                        </span>
                            {!! Form::text('search', request('search'), ['id' =>'search', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.search')]) !!}
                            <span class="input-group-append">
                            <button type="submit" id="search-btn" class="btn btn-sm btn-default">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                        </div>
                    </li>

                    <li class="nav-item mt-1 mb-1 mr-1">
                        {!! Form::select('assignee', $taskFilters, request('taskFilters', 'open'),['id'=>'task-filter','class' => ' form-control form-control-sm']) !!}
                    </li>

                    <li class="nav-item mt-1 mb-1 mr-1">
                        <select id="task-list-filter" name="status" class="form-control form-control-sm">
                            <option value="open" selected>{{ trans('fi.open') }}</option>
                            <option value="all">{{ trans('fi.all') }}</option>
                            <option value="closed">{{ trans('fi.closed') }}</option>
                            <option value="overdue">{{ trans('fi.overdue') }}</option>
                        </select>
                    </li>
                    <li class="nav-item mt-1 mb-1 mr-1">
                        {!! Form::text('date_range', null, ['id' => 'task_date_range', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.date_range')]) !!}
                        {!! Form::hidden('from_date', null, ['id' => 'task_from_date', 'class' => 'form-control input-sm']) !!}
                        {!! Form::hidden('to_date', null, ['id' => 'task_to_date', 'class' => 'form-control input-sm']) !!}
                    </li>
                    <li class="nav-item mt-1 mb-1 mr-1">
                        <button type="button" id="create-new-task" class="btn btn-sm btn-primary float-right create-task">
                            <i class="fas fa-plus"></i> {{ trans('fi.create_task') }}
                        </button>
                    </li>
                    <li class="nav-item mt-1 mb-1 mr-1">
                        <button id="reload-task" class="btn btn-sm btn-primary refresh-task text-left"
                                title="{{ trans('fi.refresh') }}">
                            <i class="fas fa-sync-alt reload-task"></i>
                        </button>
                    </li>

                    @include('tasks.widget._tasks_search_config_modal')

                    {!! Form::close() !!}

                </ul>

            </div>
        </div>
        <div class="text-center mt-3" id="task-list-container">
            <div class="spinner-border" id="task-list-container-loader" role="status">
                <span class="sr-only "> {{ trans('fi.loading') }}</span>
            </div>
        </div>
        @if(isset($taskSections) && $taskSections!= null )
            @foreach($taskSections as $id => $value)
                <div id="task-list-{{$value}}-container">
                </div>
            @endforeach
        @endif
    </div>
</div>
