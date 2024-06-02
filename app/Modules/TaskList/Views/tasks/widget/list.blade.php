<script type="text/javascript">
    function initSorting() {
        $('.task-section-box tr').mouseover(function () {
            $(this).find('.task-action-btn').removeClass('hide').addClass('inline');
            $(this).find('.movericon').css('opacity', 100);

        }).mouseout(function () {
            $(this).find('.task-action-btn').removeClass('inline').addClass('hide');
            $(this).find('.movericon').css('opacity', 0);
        });

        $('.task-section-card-header').mouseover(function () {
            $(this).find(".btn-add-task-to-section, .btn-sort-section-" + '{{$sectionSlug}}').removeClass('d-none').addClass('inline');
        }).mouseout(function () {
            if (!$(this).siblings('.card-body').find('.add-task-to-section-form').is(':visible')) {
                $(this).find(".btn-add-task-to-section, .btn-sort-section-" + '{{$sectionSlug}}').removeClass('inline').addClass('d-none');
            }
        });

        $(".task-section-list-table").sortable({
            helper: fixHelper,
            connectWith: [".task-section-list-table-sortable"],
            update: function () {
                var $_this = $(this);
                var Lists = $_this.find('.order-id');
                var reOrder = [];

                $.each(Lists, function (key, value) {
                    reOrder.push($(value).val());
                });

                reOrder.length == 0 ? $_this.css('display', 'block') : '';

                if (reOrder.length > 0) {
                    $_this.css('display', '');
                    var sectionId = $_this.attr('data-section-id');
                    var form_data = objectToFormData({ids: reOrder, task_section_id: sectionId});
                    $.ajax({
                        url: '{{ route('task.widget.reorder') }}',
                        method: 'post',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function () {
                            $_this.find('p.no-task').html("");
                        },
                    }).fail(function (response) {
                        $.each($.parseJSON(response.responseText).errors, function (id, message) {
                            alertify.error(message[0], 5);
                        });
                    });
                } else {
                    $_this.find('p.no-task').html("{{ trans('fi.no_records_found') }}");
                }

            }
        });
    }


    $('.btn-add-task-to-section').click(function () {
        $(this).closest('.card-header').siblings('.card-body').find('.add-task-to-section-form').removeClass('d-none').addClass('show');
        $(this).closest('.card-header').siblings('.card-body').find("[name='title']").focus();
    });

    $('.btn-add-task-to-section-cancel-' + '{{ $sectionSlug }}').click(function () {
        $(this).closest('.add-task-to-section-form .table').find('input:text[name=title]').val('');
        $(this).closest('.add-task-to-section-form').removeClass('show').addClass('d-none');
        $(this).closest('.task-section-box').find('.btn-add-task-to-section').removeClass('show').addClass('d-none');
    });

    $('.select2-select-box').select2();

    @if(config('fi.includeTimeInTaskDueDate') == 1 )
    var dateFormat = dateTimeFormat;
    @else
    var dateFormat = dateFormat;
    @endif

    $('#task-due-date-select-' + '{{ $sectionSlug}}').datetimepicker({
        autoclose: true,
        icons: {time: 'far fa-clock'},
        defaultDate: new Date(),
        date: new Date(),
        todayHighlight: true,
        format: dateFormat
    });

    $('.btn-add-task-to-section-submit-' + '{{ $sectionSlug }}').click(function (e) {
        e.preventDefault();
        let $this = $(this);
        let $form = $this.closest('form');

        var date = $form.find(".add-task-to-section-due-date").val();

        if (date) {
            $form.find("input[name='due_date_timestamp']").val(moment(date).format('YYYY-MM-DD HH:mm:ss'));
        } else {
            $form.find("input[name='due_date_timestamp']").val('');
        }

        $.post($form.attr('action'), $form.serializeArray(), function (response) {
            alertify.success(response.message, 1);
            $this.closest('.add-task-to-section-form').removeClass('show').addClass('d-none');
            $('#reload-task').trigger('click');
        }).fail(function (xhr) {
            let errors = JSON.parse(xhr.responseText).errors;
            $.each(errors, function (name, data) {
                alertify.error(data[0], 5);
            });
        });
    });

    var fixHelper = function (e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function (index) {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };


    $(document).off('click', ".btn-sort-section-" + '{{$sectionSlug}}').on('click', ".btn-sort-section-" + '{{$sectionSlug}}', function () {
        let $this = $(this);
        let sectionId = $this.data('section-id');
        let dir = ($(this).attr('data-dir') == 'asc') ? 'desc' : 'asc';
        $(".btn-sort-section-" + '{{$sectionSlug}}').attr('data-dir', dir);
        $.ajax({
            url: '{{ route('task.widget.sort') }}',
            method: 'post',
            data: {sectionId: sectionId, dir: dir},
            beforeSend: function () {
                showHideLoaderModal();
            },
            success: function (response) {
                $('.task-section-' + sectionId).html(response);
                showHideLoaderModal();
                initSorting();
            }
        });

    });

    initSorting();
</script>

@if($tasks)
    <div class="row">
        <div class="col-12">
            <div class="card border-top-none margin-bottom-0 task-section-box">
                <div class="card-header with-border task-section-card-header">
                    <h3 class="card-title">{{trans('fi.' . $sectionSlug) }}</h3>
                    <div class="card-tools">
                        <button type="button" title="{{ trans('fi.sort_by_due') }}"
                                class="btn btn-tool btn-tool-custom btn-outline-secondary btn-xs d-none btn-sort-section-{{$sectionSlug}}"
                                data-section-id="{{ $sectionId }}">
                            <i class="fa fa-clock"></i>
                        </button>
                        <button type="button" title="{{ trans('fi.add_item') }}"
                                class="btn btn-tool btn-tool-custom btn-outline-secondary btn-xs d-none btn-add-task-to-section"
                                data-section-id="{{ $sectionId }}">
                            <i class="fa fa-plus"></i>
                            {{ trans('fi.add_task') }}
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="task-list-container-loader d-none">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only"> {{ trans('fi.loading') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="add-task-to-section-form d-none">
                        {!! Form::open(['route' => 'task.widget.store', 'method' => 'post', 'role' => 'form', 'id' => 'task-add-to-' . $sectionSlug . '-form', 'files' => true]) !!}
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="16%">
                                    {!! Form::select('assignee_id', $users, auth()->user()->id, ['class' => 'form-control form-control-sm select2-select-box form-control-sm', 'placeholder' => trans('fi.assignee'), 'style'=> 'width:100%']) !!}
                                </td>
                                <td width="51%">
                                    {!! Form::text('title', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.title'), 'autocomplete' => 'off']) !!}
                                </td>
                                <td width="22%">
                                    <div class="input-group date task-due-date-select"
                                         id="task-due-date-select-{{$sectionSlug}}" data-target-input="nearest">
                                        {!! Form::text('due_date', null, ['class' => 'form-control form-control-sm add-task-to-section-due-date', 'placeholder' => trans('fi.due_date'), 'data-target' => '#task-due-date-select-'.$sectionSlug, 'data-toggle'=> 'datetimepicker']) !!}
                                        <div class="input-group-append"
                                             data-target="#task-due-date-select-{{$sectionSlug}}"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    {!! Form::hidden('due_date_timestamp', null, ['class' => 'due_date_timestamp']) !!}
                                </td>
                                <td width="11%" style="vertical-align: middle; text-align: end;">
                                    <button class="btn btn-xs btn-danger btn-add-task-to-section-cancel-{{ $sectionSlug }}"
                                            type="button">
                                        <i class="fa fa-trash" title="{{ trans('fi.cancel') }}"></i>
                                    </button>
                                    <button class="btn btn-xs btn-primary btn-add-task-to-section-submit-{{ $sectionSlug }}"
                                            type="button">
                                        <i class="fa fa-save" title="{{ trans('fi.save') }}"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>
                        {!! Form::hidden('task_section_id', $sectionId, ['class' => 'task_section_id form-control form-control-sm']) !!}
                        {!! Form::hidden('description', '', ['class' => 'form-control form-control-sm']) !!}
                        {!! Form::close() !!}
                    </div>
                    <div class="task_section_hidden_after_load d-block">
                        <ul class="todo-list ui-sortable task-section-list-table task-section-list-table-sortable task-section-{{ $sectionId }}"
                            data-widget="todo-list" data-section-id="{{ $sectionId }}">
                            @if(count($tasks) > 0)

                                @foreach($tasks as $task)

                                    <li class="{{ $task->is_complete ? 'done' : ''}} task-complete">
                                            <span class="handle ui-sortable-handle">
                                              <i class="fas fa-ellipsis-v"></i>
                                              <i class="fas fa-ellipsis-v"></i>
                                                  <input type="hidden" value="{{ $task->id }}" class="order-id">
                                            </span>

                                        <div class="icheck-primary d-inline ml-2"
                                             title="{{trans('fi.ctrl_plus_click')}}">
                                            <input type="checkbox" id="task_status_{{ $task->id }}" class="task-status"
                                                   data-task-title="{{ $task->title}}"
                                                   data-task-id="{{ $task->id }}"
                                                   {{ $task->is_complete ? ' checked' : '' }}
                                                   data-link="{{ route('task.complete', ['id' => $task->id , 'complete' => $task->is_complete ? '0' : '1']) }}">
                                            <label for="task_status_{{ $task->id }}"></label>
                                        </div>
                                        <i class="fa initials">
                                            {!! ($task->assignee_id) ? $task->assignee->getAvatar(26) : null !!}
                                        </i>
                                        <span class="text">{{ $task->title }}</span>
                                        @if($task->attachments->count() > 0 || $task->notes->count() > 0 || $task->client || $task->due_date)
                                            @if($task->attachments->count() > 0)
                                                <small class="badge badge-default">
                                                    <i class="fa fa-paperclip"> </i> {{ $task->attachments->count() }}
                                                </small>
                                            @endif
                                            @if($task->notes->count() > 0)
                                                <small class="badge badge-default"><i
                                                            class="fa fa-comments"> </i> {{ $task->notes->count() }}
                                                </small>
                                            @endif
                                            @if($task->client)
                                                <small class="task-list-smaller-font"> {!! $task->client ? '<a href="'.route('clients.show',$task->client).'">'.$task->client->name.'</a>' : '' !!}</small>
                                            @endif
                                            @if($task->due_date)
                                                <small class="task-list-smaller-font float-right d-block task-action-date d-block pt-1
                                                    {!! ($task->overdue && !$task->is_complete ? 'task-overdue' : ($task->dueToday && !$task->is_complete ? 'task-current' : 'task-future'))!!}"
                                                       title="{!! $task->formatted_due_date !!}">
                                                    <i class="fa fa-clock"></i>
                                                    {{ $task->formatted_as_due_date }} </small>
                                            @endif
                                        @endif
                                        <div class="tools pr-2">
                                            @if(!$task->is_complete)
                                                <button class="btn btn-xs btn-outline-primary btn-edit-task task-action-btn task-edit-btn d-done"
                                                        data-link="{{ route('task.widget.edit', ['id' => $task->id]) }}">
                                                    <i class="fas fa-edit"
                                                       title="{{ trans('fi.edit') }}"></i>
                                                </button>
                                            @endif

                                            <button class="btn btn-xs btn-outline-danger btn-delete-task task-action-btn task-delete-btn d-done"
                                                    data-action="{{ route('task.delete', ['id' => $task->id]) }}">
                                                <i class="fas fa-trash"
                                                   title="{{ trans('fi.delete') }}"></i>
                                            </button>

                                        </div>
                                    </li>

                                @endforeach

                                <p class="no-task"></p>
                            @else
                                <p class="no-task">{{ trans('fi.no_records_found') }}</p>
                            @endif
                        </ul>
                    </div>
                    <div class="col-12 pull-right">
                        <div class="dynamic-pages float-right pagination-nav-css" data-section-id="{{$sectionId}}"
                             data-section-name="{{$sectionSlug}}">
                            {{ $tasks->onEachSide(0)->links() }}
                        </div>
                    </div>
                    <div class="pull-left">
                        <div id="tasks-filter">
                            @if(request('search') || (request('status') && request('status') != 'open'))
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif