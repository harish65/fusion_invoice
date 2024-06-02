<script type="text/javascript">
    $(function () {

        $('body #task-completion-note').modal();

        $('#task-complete-with-note-save').click(function () {

            var $_this = $(this);
            let data =
                {
                    id: ($_this).data('task-id'),
                    completion_note: $('#completion_note1').val()
                };
            $.post($_this.data('url'), data)
                .done(function (response) {
                    if (response.success == true) {
                        alertify.success(response.message, 5);
                        $('#task-completion-note').modal('hide');
                        $('#modal_note_content').val('');
                        @if($widgetDashboard == '1')
                            let $_this = $('#task_status_' + response.task_id);
                            let oldLink = $_this.data('link').slice(0, -1);
                            $_this.attr('data-link', oldLink + 0);
                            $_this.closest('li').find('.btn-edit-task').addClass('disabled').hide();
                            $_this.closest('li').addClass('done');
                            $_this.closest('li').find('#task_status_' + response.task_id).prop('checked', true);
                        @endif
                    } else {
                        alertify.error(response.message, 5);
                    }
                    if ('{{isset($redirectUrl) && $redirectUrl != null }} ') {
                        window.location.replace('{{$redirectUrl}}');
                    }
                }).fail(function (response) {
                alertify.error(response.message, 5);
            });
            window.location.reload();
        });
    });
</script>

<div class="modal fade" id="task-completion-note" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{ trans('fi.completion-note') }} : {{$title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::textarea('completion_note', isset($completionNote) ? $completionNote : null, ['id' => 'completion_note1', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.placeholder_type_note'), 'rows' => 3,'autofocus'=>"true"]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.close') }}</button>
                <button type="button" id="task-complete-with-note-save" data-task-id="{{$id}}"
                        class="btn btn-sm btn-primary" data-url="{{ route('task.complete-with-note') }}">
                    <i class="fa fa-save"></i>
                    {{ trans('fi.save-and-complete') }}
                </button>
            </div>
        </div>
    </div>
</div>

