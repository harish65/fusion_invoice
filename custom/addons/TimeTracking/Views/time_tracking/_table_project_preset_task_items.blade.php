<script>
    $(function () {
        $(".preset-task-items-sortable").sortable();
        $("#sortable").sortable({
                update: function () {
                    var Lists = $(this).children('li');

                    var reOrder = [];

                    $.each(Lists, function (key, value) {
                        reOrder.push($(this).data('id')+'###'+key);
                    });
                    var form_data = objectToFormData({ids: reOrder});

                    $.ajax({
                        url: '{{ route('timeTracking.projects.project.task.lists.reorder') }}',
                        method: 'post',
                        data: form_data,
                        processData: false,
                        contentType: false
                    }).done(function () {
                        alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                    }).fail(function (response) {
                        $.each($.parseJSON(response.responseText).errors, function (id, message) {
                            alertify.error(message[0], 5);
                        });
                    });

                }
            }
        );
    });
</script>
@if(count($items) > 0)
    <ul id="sortable" class="todo-list ui-sortable" data-widget="todo-list">
        @foreach($items as $key => $item)
            <li class="ui-sortable-handle p-1" data-id="{{$item->id}}">
                <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </span>

                <span class="text">{{$item->task_name}} {!! (isset($taskItemsCount[$item->id])) ? ' <span class="badge badge-primary preset-task-items-count" title="total items">'. $taskItemsCount[$item->id]['items_count'].'</span>' : '' !!}</span>
                <div class="tools">
                    <button class="btn btn-xs btn-outline-primary preset-task-item-edit task-action-btn task-edit-btn d-done"
                            data-id="{{$item->id}}" data-text="{{$item->task_name}}"
                            data-original="{{trans('fi.add')}}"
                            data-duplicate="{{trans('fi.edit')}}">
                        <i class="fas fa-edit" title="Edit"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger task-action-btn task-delete-btn d-done
                            preset-task-item-delete" href="#" data-id="{{$item->id}}"
                            data-action="{{route('timeTracking.projects.preset.task.item.delete',['id'=>$item->id])}}">
                        <i class="fas fa-trash" title="Delete"></i>
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <div class="col-md-12 text-center">
        <span><strong>{{trans('fi.no_records_found')}}</strong></span>
    </div>
@endif
