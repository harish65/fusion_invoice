<script type="text/javascript">
    $(function () {

        $('#modal-show-preset-task-items').modal();

        $('.btn-preset-task-items-save').click(function () {

            showHideLoaderModal();
            var $document = $(document);
            var id = $document.find('.btn-preset-task-items-save').attr("data-item-id");
            $.post('{{ route('timeTracking.projects.preset.task.item.store') }}', {
                time_tracking_preset_tasks_id: $('#preset-tasks-name').val(),
                task_name: $('#task-name').val(),
                id: id,
            }).done(function (response) {
                alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                presetTaskItems($('#preset-tasks-name').val());
                showHideLoaderModal();
                $('.toggle-name').text($('.toggle-name').data('original'));
                presetTasksDynamic();
            }).fail(function (response) {
                showHideLoaderModal();
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });

        });

        $('#preset-tasks-name').change(function () {
            if (($(this).val()).length > 0) {
                $('.task-items-toggles').removeClass('d-none').addClass('d-block');
            } else {
                $('.task-items-toggles').addClass('d-none').removeClass('d-block');
            }
            presetTaskItems($(this).val());
        });

        @if(isset($presetId) && $presetId != null)
        $('.task-items-toggles').removeClass('d-none').addClass('d-block');
        presetTaskItems('{{$presetId}}');
        @endif

        function presetTaskItems(id) {
            var $document = $(document);
            var action = $document.find('.btn-preset-task-items-save').attr("data-original");
            $document.find('.btn-preset-task-items-save').html(action);
            $document.find('.btn-preset-task-items-save').attr("data-item-id", '');
            $document.find('#task-name').val('');
            $.post('{{ route('timeTracking.projects.get.preset.task.items') }}', {
                time_tracking_preset_tasks_id: id,
            }).done(function (response) {
                $('.preset-task-items').html(response);
            }).fail(function (response) {
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        }

        function presetTasksDynamic() {
            var $document = $(document);
            var action = $document.find('.btn-preset-tasks-save').attr("data-original");
            $document.find('.btn-preset-tasks-save').html(action);
            $document.find('.btn-preset-tasks-save').attr("data-item-id", '');
            $document.find('#list-name').val('');
            $.get('{{ route('timeTracking.projects.get.preset.tasks') }}').done(function (response) {
                $('.preset-tasks-dynamic').html(response);
            }).fail(function (response) {
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        }

        $(document).off("click", ".preset-task-item-edit").on('click', '.preset-task-item-edit', function () {
            var $_this = $(this);
            var $document = $(document);
            var id = $_this.data('id');
            $document.find('.btn-preset-task-items-save').html($_this.data('duplicate'));
            $document.find('.btn-preset-task-items-save').attr("data-item-id", id);
            $document.find('#task-name').val($_this.data('text'));
            $document.find('.toggle-name').html('{{trans('TimeTracking::lang.edit_item_name')}}');
        });

        $(document).off("click", ".preset-task-item-delete").on('click', '.preset-task-item-delete', function () {

            $(this).addClass('delete-preset-task-items-active');

            $('#modal-placeholder').load('{!! route('timeTracking.projects.preset.task.item.delete.modal') !!}', {
                    action: $(this).data('action'),
                    modalName: 'preset-task-items',
                    isReload: false,
                    returnURL: window.location.href
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );

        });


    });
</script>

<div class="modal fade" id="modal-show-preset-task-items" style="z-index: 99999; position: absolute; display: block;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('TimeTracking::lang.maintain_preset_project_task_lists') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body table-responsive">

                <div id="modal-status-placeholder"></div>

                <div class="row">
                    <div class="col-md-2">
                        <label>{{ trans('TimeTracking::lang.task_list_name') }}</label>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('preset_task_names', $presetTaskNames,isset($presetId) ? $presetId : null, ['id' => 'preset-tasks-name','class' => 'form-control form-control-sm']) !!}
                        </div>
                    </div>
                    {!! Form::hidden('project_id', isset($projectId) ? $projectId : null, ['id' => 'project-id']) !!}
                </div>
                <div class="task-items-toggles d-none">
                    <div class="row mt-1">
                        <div class="col-md-2">
                            <label data-original="{{ trans('TimeTracking::lang.new_item_name') }}"
                                   class="toggle-name">{{ trans('TimeTracking::lang.new_item_name') }}</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('task_name', null, ['id' => 'task-name','class' => 'form-control form-control-sm mb-2']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-primary btn-preset-task-items-save"
                                    data-original="{{trans('fi.add')}}" data-item-id="">
                                {{trans('fi.add')}}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 task-items-toggles d-none">
                    <div class="col-md-12">
                        <h5 class="">{{trans('TimeTracking::lang.list_items')}}</h5>
                        <div class="preset-task-items preset-task-items-sortable"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('TimeTracking::lang.close') }}</button>
            </div>
        </div>
    </div>
</div>