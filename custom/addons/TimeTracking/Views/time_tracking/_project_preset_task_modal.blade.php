<script type="text/javascript">
    $(function () {

        $('#btn-preset-tasks-save').modal();

        $('[data-toggle="tooltip"]').tooltip({
            'delay': {show: 1100, hide: 100}
        });

        $('.btn-preset-tasks-save').click(function () {
            var $document = $(document);
            var id = $document.find('.btn-preset-tasks-save').attr("data-item-id");
            showHideLoaderModal();
            $.post('{{ route('timeTracking.projects.preset.task.store') }}', {
                list_name: $('#list-name').val(),
                project_id: $('#project-id').val(),
                id: id,
            }).done(function (response) {
                alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                showHideLoaderModal();
                presetTasksDynamic()
                $('.toggle-name').text($('.toggle-name').data('original'));
            }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        });

        $(document).off("click", ".btn-preset-task-edit").on('click', '.btn-preset-task-edit', function () {
            var $_this = $(this);
            var $document = $(document);
            var id = $_this.data('id');
            $document.find('.btn-preset-tasks-save').html($_this.data('duplicate'));
            $document.find('.btn-preset-tasks-save').attr("data-item-id", id);
            $document.find('#list-name').val($_this.data('text'));
            $document.find('#list-name').focus();
            $document.find('.toggle-name').html('{{trans('TimeTracking::lang.edit_name')}}');
        });

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

        presetTasksDynamic();

        $(document).off("click", ".btn-maintain-preset-task").on('click', '.btn-maintain-preset-task', function () {
            $('#modal-addon-timeTracking-preset-task-items').load('{!! route('timeTracking.projects.preset.task.item.modal') !!}', {
                    'project_id': '{{isset($projectId) ? $projectId : null}}',
                    'preset_id': $(this).data('id'),
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });

        $(document).off("click", ".btn-preset-task-delete").on('click', '.btn-preset-task-delete', function () {

            $(this).addClass('delete-preset-tasks-active');

            $('#modal-placeholder').load('{!! route('timeTracking.projects.preset.task.delete.modal') !!}', {
                    action: $(this).data('action'),
                    modalName: 'preset-tasks',
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

<div class="modal fade" id="btn-preset-tasks-save">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('TimeTracking::lang.time_tracking_preset_tasks') }}</h4>
                <i class="fa fa-question-circle ml-3" data-toggle="tooltip" data-placement="auto" title="{!! trans('TimeTracking::lang.tt_about_preset_tasks') !!}"></i>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="toggle-name"
                               data-original="{{trans('TimeTracking::lang.list_name')}}">{{ trans('TimeTracking::lang.list_name') }}</label>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            {!! Form::text('list_name', null, ['id' => 'list-name','class' => 'form-control form-control-sm mb-2']) !!}
                        </div>
                    </div>
                    <div class="col-md-4 ml-auto">
                        <button class="btn btn-sm btn-primary btn-preset-tasks-save" data-item-id=""
                                data-original="{{trans('fi.add')}}">
                            {{trans('fi.add')}}
                        </button>
                    </div>
                    {!! Form::hidden('project_id', isset($projectId) ? $projectId : null, ['id' => 'project-id']) !!}
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-sm table-hover table-striped">
                            <thead>
                            <tr>
                                <th width="60%">{{trans('TimeTracking::lang.preset_task_lists')}}</th>
                                <th width="35%" class="text-right">{{trans('fi.action')}}</th>
                            </tr>
                            </thead>
                            <tbody class="preset-tasks-dynamic"></tbody>
                        </table>
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