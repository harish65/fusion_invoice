@include('tasks.widget._js_tasks_list_widget')
<script type="text/javascript">

    $(function () {
        $('#task-filter > option').each(function () {
            var $_this = $(this);
            if ($_this.val() == $('#cookie_assignee').val()) {
                $_this.prop('selected', true);
            } else {
                $_this.prop('selected', false);
            }
        });
        $('#task-list-filter > option').each(function () {
            var $_this = $(this);
            if ($_this.val() == $('#cookie_status').val()) {
                $_this.prop('selected', true);
            } else {
                $_this.prop('selected', false);
            }
        });

        if ($('#task_from_date').val() == '') {
            $('#task_from_date').val($('#cookie_date_from').val());
        }

        if ($('#task_to_date').val() == '') {
            $('#task_to_date').val($('#cookie_date_to').val());
        }

        if ($('#cookie_date_from').val() != '' && $('#cookie_date_to').val() != '') {
            $('#task_date_range').val(($('#cookie_date_from').val() + '-' + $('#cookie_date_to').val()));
        }

        if ($('#search').val() == '' && $('#cookie_search').val() != '') {
            $('#search').val($('#cookie_search').val());
        }

        var populateTaskList = function (link) {
            $('#task-list-container').load(link);
        };
        let $body = $('body');

        $('#create-new-task').click(function () {
            $('#modal-placeholder').load('{{ route('task.widget.create') }}');
            $(this).prop('disabled',true);
        });

        $body.on('click', '.task-status', function (event) {

            if (event.ctrlKey) {
                let id = $(this).data('task-id');
                var url = '{{ route("task.complete-with-note.modal",[":id"] ) }}';
                url = url.replace(':id', id);
                $('#note-modal-placeholder').load(url, {widget: 1});
                return false;
            } else {
                let $this = $(this);
                let id = $this.attr('id').replace('_edit', '');
                let $completePost = $.post($this.attr('data-link'));
                $completePost.done(function () {
                    let $_this = $('#' + id);
                    if ($this.is(':checked')) {
                        $_this.prop("checked", true);
                    } else {
                        $_this.prop("checked", false);
                    }

                    let oldLink = $_this.data('link').slice(0, -1);
                    let status = $_this.is(':checked') ? 0 : 1;
                    $_this.attr('data-link', oldLink + status);

                    if ($_this.is(':checked')) {

                        $_this.closest('li').find('.btn-edit-task').addClass('disabled').hide();
                        $_this.closest('li').addClass('done');
                    } else {
                        $_this.closest('li').find('.btn-edit-task').removeClass('disabled').show();
                        $_this.closest('li').removeClass('done');
                    }

                    $('#search-btn').trigger('click');

                });
                $completePost.fail(function (xhr, status, error) {
                    alertify.error(error, 5);
                });
            }
        });

        $body.on('click', '.btn-edit-task', function () {
            if (!$(this).hasClass('disabled')) {
                $('#modal-placeholder').load($(this).data('link'));
            }
        }).on('click', '.sortable-task-list-header a', function (e) {
            e.preventDefault();
            populateTaskList($(this).attr('href'));
        });

        $body.on('click', '#btn-clear-filters', function () {
            $('#search, #task_date_range, #task_from_date, #task_to_date').val('');
            $('#task-list-filter').prop('selectedIndex', 0);
            $('#task-filter').prop('selectedIndex', 0);
            $("#tasks-filter-form").submit();
        });

        $body.on('click', '#reload-task', function (e) {
            e.preventDefault();
            $('.reload-task').addClass('fa-spin')
            setTimeout(function () {
                $('.reload-task').removeClass('fa-spin')
            }, 1500);
            $.ajax({
                url: '{{ route('task.widget.refresh') }}',
                method: 'post',
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function () {
                    showHideLoaderModal();
                    $('#search, #task_date_range, #task_from_date, #task_to_date').val('');
                    $('#task-list-filter').prop('selectedIndex', 0);
                    $('#task-filter').prop('selectedIndex', 0);
                    $("#tasks-filter-form").submit();
                }
            });
        });

        $body.on('click', '.btn-delete-task', function () {
            var $_this = $(this);

            $(this).addClass('delete-task-active');

            $('#modal-placeholder').load('{!! route('task.delete.modal') !!}', {
                    action: $_this.data('action'),
                    modalName: 'task',
                    widgetTask: false,
                    isReload: false,
                    returnURL: document.URL
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });

        $('.custom-search').click(function () {
            $("#tasks-filter-form").submit();
            $('#modal-search-config').modal('hide');
        });

        $('.close-search-config-modal').click(function () {
            $('#modal-search-config').modal('hide');
        });

        $('#task-list-filter,#task-filter,#task_date_range').change(function () {
            $('.task-list-container-loader').removeClass('d-none');
            $('.task_section_hidden_after_load').removeClass('d-block');
            $('.task-list-container-loader').addClass('d-block');
            $('.task_section_hidden_after_load').addClass('d-none');
            $("#tasks-filter-form").submit();
        });

        $('.search-config-chk').change(function () {
            let checked = false;
            $.each($(".search-config-chk:checked"), function () {
                checked = true;
            });

            if (checked == false) {
                $('#search-config-btn').addClass('btn-danger').closest('.input-group').addClass('has-error');
            } else {
                $('#search-config-btn').removeClass('btn-danger').closest('.input-group').removeClass('has-error');
            }
        });

        $('#tasks-filter-form').submit(function (e) {
            e.preventDefault();
            let $form = $(this);
            let data = $form.serializeArray();
            let checked = false;
            $.each($(".search-config-chk:checked"), function () {
                checked = true;
            });
            if (checked == true) {
                @if(isset($taskSections) && $taskSections!= null )
                @foreach($taskSections as $id => $value)
                data.push({name: 'taskSection', value: '{{$id}}'});
                $.post('{{route('task.widget.list')}}', data, function (response) {
                    $('#task-list-' + '{{$value}}' + '-container').html(response);
                });
                data.pop('taskSection');
                @endforeach
                $('#task-list-container').remove();
                @endif
            }
        });

        $('#search-btn').trigger('click');

        $('#search-btn').click(function (e) {
            e.preventDefault();
            $('.task-list-container-loader').removeClass('d-none');
            $('.task_section_hidden_after_load').removeClass('d-block');
            $('.task-list-container-loader').addClass('d-block');
            $('.task_section_hidden_after_load').addClass('d-none');
            $('#tasks-filter-form').submit();
        });

        initDateRangePicker('task');


    });
</script>