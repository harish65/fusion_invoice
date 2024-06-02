@if(count($presetTasks) > 0)
    @foreach($presetTasks as $key => $presetTask)
        <tr>
            <td>
                {{$presetTask->list_name}}{!! (isset($taskItemsCount[$presetTask->id])) ? ' <span class="badge badge-primary preset-task-items-count ml-1" title="'.trans('TimeTracking::lang.items_count').'">'. $taskItemsCount[$presetTask->id]['items_count'].'</span>' : '' !!}
            </td>

            <td class="text-right">
                <div class="card-tools">
                    @if(!$editMode)
                        <button type="button" class="btn btn-tool {{ (isset($taskItemsCount[$presetTask->id]) && $taskItemsCount[$presetTask->id]['items_count'] > 0 ? 'btn-preset-task-apply' : 'disabled') }}" title="{{ trans('fi.apply') }}"
                                data-id="{{$presetTask->id}}" {{ (isset($taskItemsCount[$presetTask->id]) && $taskItemsCount[$presetTask->id]['items_count'] > 0 ? '' : 'disabled') }}>
                            <i class="fa fa-magic {{ (isset($taskItemsCount[$presetTask->id]) && $taskItemsCount[$presetTask->id]['items_count'] > 0 ? 'text-success' : 'text-default') }}"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-tool btn-maintain-preset-task"
                                title="{{ trans('TimeTracking::lang.list_items') }}"
                                data-id="{{$presetTask->id}}">
                            <i class="fa fa-tasks text-primary"></i>
                        </button>

                        <button type="button" class="btn btn-tool btn-preset-task-edit text-primary"
                                title="{{ trans('TimeTracking::lang.edit_list_name') }}"
                                data-id="{{$presetTask->id}}"
                                data-text="{{$presetTask->list_name}}"
                                data-original="{{trans('fi.add')}}"
                                data-duplicate="{{trans('fi.save')}}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-tool btn-preset-task-delete"
                                title="{{ trans('TimeTracking::lang.delete_list') }}"
                                data-action="{{route('timeTracking.projects.preset.task.delete',['id'=>$presetTask->id])}}"
                                data-id="{{$presetTask->id}}">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <div class="col-md-12 text-center">
        <span><strong>{{trans('fi.no_records_found')}}</strong></span>
    </div>
@endif