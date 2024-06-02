<script type="text/javascript">
    $(document).ready(function ($) {
        $('#add-timeline-note').click(function () {
            $('#note-modal-placeholder').load('{{ route('notes.create') }}');
            $('#add-timeline-note').prop('disabled',true);
        });

        @if (!auth()->user()->client_id)

        $('.note-item-edit').click(function () {
            let editLink = $(this).data('edit-link');
            $('#note-modal-placeholder').load(editLink);
        });

        $(document).off("click",".note-item-delete").on("click",".note-item-delete",function() {

            let $ele = $(this);

            $ele.addClass('delete-notes-active');

            $('#modal-placeholder').load('{!! route('notes.delete.modal') !!}', {
                    action: $(this).data('action'),
                    modalName: 'notes',
                    isReload: false,
                    returnURL: null
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );

        });
        @endif

        $('.custom-search').click(function () {
            $("#notes-filter-form").submit();
            $('#modal-search-config').modal('hide');
        });

        $('.close-search-config-modal').click(function () {
            $('#modal-search-config').modal('hide');
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

        $('#notes-filter-form').submit(function (e) {
            e.preventDefault();
            let $form = $(this);
            let url = $form.attr('action');
            let data = $form.serializeArray();
            let checked = false;
            $.each($(".search-config-chk:checked"), function () {
                checked = true;
            });
            if (checked == true) {
                $.get(url, data, function (response) {
                    $('#note-timeline-container').html(response);
                });
            }
        });

        $('#btn-clear-notes-filter').click(function () {
            $('#note-timeline-container').load('{{ route('notes.list', [$model, $object->id, $showPrivateCheckbox, 'description' => !Session::has('filter_by_description') ? '1' : (Session::get('filter_by_description') == 1 ? '1' : '0'), 'tags' => !Session::has('filter_by_tags') ? '1' : (Session::get('filter_by_tags') == 1 ? '1' : '0'), 'username' => !Session::has('filter_by_username') ? '1' : (Session::get('filter_by_username') == 1 ? '1' : '0')]) }}');
        });

        $('.note-collapsed').click(function () {
            if ($(this).attr('aria-expanded') == 'false') {
                var text = '{{ trans("fi.show_less") }}';
            } else {
                var text = '{{ trans("fi.show_more") }}';
            }
            $(this).text(text);
        });
    });
</script>
<div id="fi-notepad" data-model="{{ $model }}" data-object-id="{{ $object->id }}" data-can-be-private="{{ $showPrivateCheckbox }}">
    <div class="card">
        <div class="card-header">
            <h2 class="d-inline"><i class="far fa-comments"></i> {{ trans('fi.notepad') }}</h2>
            @if($notes->total() > 0)
                <span class="badge badge-success note-count">{{ $notes->total() }}</span>
            @endif
            <div class="card-tools">
                {!! Form::open(['method' => 'GET', 'url' => route('notes.list', [$model, $object->id, $showPrivateCheckbox]), 'id' => 'notes-filter-form', 'class' => 'form-inline inline m-0']) !!}
                <div class="input-group input-group-sm">
                    <span class="input-group-prepend">
                        <button type="button" data-toggle="modal" data-target="#modal-search-config" id="search-config-btn" class="btn btn-sm btn-default"><i class="fa fa-ellipsis-v"></i></button>
                    </span>
                    {!! Form::text('search', request('search'), ['id' =>'search-notes', 'class' => 'form-control inline form-control-sm','placeholder' => trans('fi.search')]) !!}
                    <span class="input-group-append">
                    <button type="submit" id="search-btn" class="btn btn-sm btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
                </div>
                @can('notes.create')
                <button type="button" class="btn btn-sm btn-primary ml-1 " id="add-timeline-note">
                    <i class="fa fa-plus"></i> {{ trans('fi.add_note') }}
                </button>
                @endcan
                @include('notes._notes_search_config_modal')
                {!! Form::close() !!}

            </div>
        </div>
        <div class="card-body">
            <div class="timeline timeline-notes small">
                @foreach ($notes as $note)
                    <div id="note-timeline-item-{{ $note->id }}">
                        <i title="{{$note->user->name}}" class="fas">
                            {!! $note->user != null ? $note->user->getAvatar(30) : '' !!}
                        </i>

                        <div class="timeline-item">
                            <span class="time">
                                <i class="fas fa-clock"></i>
                                <span title="{{ $note->formatted_created_at_system_format }}"> {{ $note->formatted_created_at }}</span>
                            </span>

                            <h3 class="timeline-header">
                                {!! $note->user != '' ? $note->user->formatted_name : '' !!}

                                @if($note->updatedBy)
                                    <span class="time-item" title="{{ $note->formatted_updated_at_system_format }}">
                                                {{ trans('fi.last_edited') }}
                                        : {{ $note->updatedBy->name }} {{ $note->formatted_updated_at }}
                                    </span>
                                @endif
                                <span class="note-tags pull-right">
                                    @if (isset($showPrivateCheckbox) and $showPrivateCheckbox == true)
                                        @if ($note->private)
                                            <span class="badge badge-danger">{{ trans('fi.private') }}</span>
                                        @else
                                            <span class="badge badge-success">{{ trans('fi.public') }}</span>
                                        @endif
                                    @endif
                                    @foreach($note->tags as $noteTag)
                                        <span class="badge badge-info">{{ $noteTag->tag->name }}</span>
                                    @endforeach
                                </span>
                            </h3>

                            <div class="timeline-body">
                                {!! $note->note !!}
                            </div>
                            <div class="timeline-footer">
                                <div class="text-right">
                                    @if(Gate::check('notes.update') || Gate::check('notes.delete'))
                                        @can('notes.update')
                                        <a href="#" class="btn btn-sm btn-primary note-item-edit"
                                           data-edit-link="{{ route('notes.edit', ['id' => $note->id]) }}">
                                            <i class="fa fa-edit" title="{{ trans('fi.edit_note') }}"></i>
                                        </a>
                                        @endcan
                                        @can('notes.delete')
                                        <a href="#" class="btn btn-sm btn-danger note-item-delete"
                                           data-note-id="{{ $note->id }}">
                                            <i class="fa fa-trash" title="{{ trans('fi.delete') }}"></i>
                                        </a>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer">
            <div class="pull-left" style="padding-left: 15px;;">
                @if(request('search'))
                    <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $notes->total(),'plural' => $notes->total() > 1 ? 's' : '']) }}
                    <button type="button" class="btn btn-sm btn-link" id="btn-clear-notes-filter">{{ trans('fi.clear') }}</button>
                @endif
            </div>
            <div class="pull-right" id="notes-pagination" style="padding-right: 25px;">
                {{ $notes->links() }}
            </div>
        </div>
    </div>
</div>