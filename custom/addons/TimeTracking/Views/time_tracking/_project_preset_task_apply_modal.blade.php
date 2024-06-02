<script type="text/javascript">
    $(function () {

        $('#modal-show-preset-task-apply').modal();

        $(document).off("click", ".btn-preset-task-apply").on('click', '.btn-preset-task-apply', function () {
            showHideLoaderModal();

            var $_this = $(this);
            $.post('{{ route('timeTracking.projects.preset.task.apply') }}', {
                project_id: $('#project-id').val(),
                id: $_this.data('id'),
            }).done(function (response) {
                alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                showHideLoaderModal();
                $('#modal-show-preset-task-apply').modal('hide');
                window.location.reload();
            }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });

        });

        function presetTasksApplyDynamic() {
            $('#list_name').val('');
            $.get('{{ route('timeTracking.projects.get.preset.tasks.apply') }}').done(function (response) {
                $('.preset-tasks-apply-dynamic').html(response);
            }).fail(function (response) {
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        }

        presetTasksApplyDynamic();

    });
</script>

<div class="modal fade" id="modal-show-preset-task-apply">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('TimeTracking::lang.time_tracking_preset_tasks') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body table-responsive">

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm table-hover table-striped">
                            <thead>
                            <tr>
                                <th width="60%">{{trans('fi.name')}}</th>
                                <th width="35%" class="text-right">{{trans('fi.action')}}</th>
                            </tr>
                            </thead>
                            <tbody class="preset-tasks-apply-dynamic"></tbody>
                        </table>
                        {!! Form::hidden('project_id', isset($projectId) ? $projectId : null, ['id' => 'project-id']) !!}
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