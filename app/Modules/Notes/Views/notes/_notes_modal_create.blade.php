@include('layouts._summernote')
@include('layouts._select2')
<script type="text/javascript">

    $(function () {

        @if(config('fi.includeTimeInTaskDueDate') == 1 )
            var dateFormat = dateTimeFormat;
        @else
            var dateFormat = dateFormat;
        @endif

        document.emojiSource = "{{ asset('assets/plugins/tam-emoji/img/') }}";
        $('#modal_note_content').summernote({
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['color', ['color']],
                ['para', ['ul', 'ol']],
                ['insert', ['link', 'picture', 'emoji']],
                ['table', ['table']],
                ['view', ['codeview']],
            ]
        });
        let canBePrivate = $('#fi-notepad').data('can-be-private');
        let isPrivate = 0;
        let isCreateTask = 0;
        let dueDateTimestamp = null;
        let date = null;

        $('#note-task-duedate').datetimepicker({
            defaultDate: new Date(),
            date: new Date(),
            icons: {time: 'far fa-clock'},
            format: dateFormat,
            autoclose: true
        });

        if (!canBePrivate) {
            $('#is-private-container').remove();
        }

        $('#create-note').modal();

        $("#create-note").on('shown.bs.modal', function () {
            $('#modal_note_content').focus();
        });

        $('#note-tags').select2({tags: true, tokenSeparators: [",", " "]});

        $('#create_task').on('change', function () {
            let $this = $(this);

            if ($this.is(':checked')) {
                $('.due-date').show();
            } else {
                $('.due-date').hide();
            }
        });
        $('.fa-calendar').click(function () {
            $('#note-task-duedate').focus();
        });
        $('#note-task-due-date').blur(function () {
            $('#note-task-duedate').datetimepicker('hide');
        });
        $('#note-create-confirm').click(function () {
            var $_this = $(this);

            if (canBePrivate) {
                isPrivate = $('#private').is(':checked') ? 1 : 0;
            }

            @if($editMode == false)
                isCreateTask = $('#create_task').is(':checked') ? 1 : 0;
            @if(config('fi.includeTimeInTaskDueDate') == 1)
                date = $('#note-task-due-date').val();
            @else
                date = $('#note-task-due-date').datepicker().val();
            @endif
            if (date) {
                dueDateTimestamp = moment(date).format('YYYY-MM-DD HH:mm:ss');
            }
            @endif
            let data = {
                model: $('#fi-notepad').data('model'),
                model_id: $('#fi-notepad').data('objectId'),
                note: $('#modal_note_content').val(),
                create_task: isCreateTask,
                isPrivate: isPrivate,
                showPrivateCheckbox: canBePrivate,
                isTimeLine: true,
                isPublicView: '{{ $isPublicView }}',
                tags: $('#note-tags').val(),
                due_date: $('#note-task-due-date').val(),
                title: $('#note-task-title').val(),
                due_date_timestamp: dueDateTimestamp,
            };

            $_this.prop('disabled', true).addClass('disabled');

            $.post($_this.data('url'), data).done(function (response) {
                $('#create-note').modal('hide');
                $('#modal_note_content').val('');
                $('#note-timeline-container').html(response);
                $(this).removeAttr('disabled').removeClass('disabled');
                @if(!$editMode)
                let notesCount = Number($('#notes-count').text()) + 1;
                if (0 < notesCount) {
                    $('#notes-count').html(Number(notesCount)).show().removeClass('hide');
                } else {
                    $('#notes-count').html('').hide().addClass('hide');
                }
                @endif
                if (typeof $.fn.loadTimelineList == 'function') {
                    $.fn.loadTimelineList();
                }

            }).fail(function (response) {
                $_this.prop('disabled', false).removeClass('disabled');
                $.each($.parseJSON(response.responseText).errors, function (id, message) {
                    alertify.error(message[0], 5);
                });
            });
        });
        $('.note-modal-close').click(function (){
            $('#add-timeline-note').prop('disabled', false);
            $('#client-create-note-transition').removeClass('disabled');
            $('#client-create-note').removeClass('disabled');
        });
    });
</script>
<div class="modal fade" id="create-note" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.note') }}</h5>
                <button type="button" class="close note-modal-close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <div class="row">

                    <div class="col-md-8">
                        <div class="form-group">
                            <textarea placeholder="{{ trans('fi.placeholder_type_message') }}"
                                      class="form-control form-control-sm" autofocus="true" style="resize: none;"
                                      id="modal_note_content" rows="12">{{ $note }}</textarea>
                        </div>
                        <div class="form-group" id="is-private-container">
                            <label>
                                <input type="checkbox" name="private" id="private"
                                       value="{{ $isPrivate }}" {{ true == $isPrivate ? 'checked' : '' }}> {{ trans('fi.private') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>{{ trans('fi.tags') }}: </label>

                        <div class="form-group" id="note-tags-container">
                            {!! Form::select('tags[]', $tags, $selectedTags, ['class' => 'form-control form-control-sm','multiple' => true, 'id' => 'note-tags', 'style' => 'width: 100%;']) !!}
                        </div>
                    </div>

                    @if($editMode == false)
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="create_task" id="create_task"
                                           value="1">
                                    <label for="create_task"
                                           class="form-check-label">{{ trans('fi.task_from_note') }}</label>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="due-date row" style="display: none;">

                    <div class="col-md-8">
                        <div class="form-group">
                            <label>{{ trans('fi.title') }}:</label>
                            {!! Form::text('title', null, ['class' => 'form-control form-control-sm', 'id' => 'note-task-title', 'placeholder' => trans('fi.title'), 'autocomplete' => 'off']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ trans('fi.due_date') }}:</label>

                            <div class="input-group date" id="note-task-duedate" data-target-input="nearest">
                                {!! Form::text('due_date', null, ['class' => 'form-control form-control-sm', 'id' => 'note-task-due-date', 'placeholder' => trans('fi.due_date'), 'data-toggle' => 'datetimepicker','data-target'=>"#note-task-duedate" ]) !!}
                                <div class="input-group-append" data-target="#note-task-duedate"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default note-modal-close"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                @if($editMode)
                    <button type="button" id="note-create-confirm" class="btn btn-sm btn-primary"
                            data-url="{{ route('notes.update', ['id' => $noteId]) }}"><i class="fa fa-save"></i>&nbsp;&nbsp;{{ trans('fi.save') }}
                    </button>
                @else
                    <button type="button" id="note-create-confirm" class="btn btn-sm btn-primary note-modal-close"
                            data-url="{{ route('notes.create') }}"><i
                                class="fa fa-save"></i>&nbsp;&nbsp;{{ trans('fi.save') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>