@include('layouts._select2')
@include('layouts._bootstrap-multiselect')
<script type="text/javascript">
    $(function () {

        $.fn.loadTimelineList = function (page = 1) {
            let $form = $('#transitions-filter-form');
            let data = $form.serializeArray();
            data.push({name: 'page', value: page});
            let passUrl = "{{route("user.transition.list",['client' => $clientId])}}"
            $.ajax({
                url: passUrl,
                method: 'post',
                data: data,
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function (response) {
                    showHideLoaderModal();
                    $('#timeline-container').html(response);
                },
                error: function () {
                    showHideLoaderModal();
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
        }

        $('#selectUser').on('change', function () {
            return $.fn.loadTimelineList();
        });
        $('#entity-selection').on('change', function () {
            return $.fn.loadTimelineList();
        });

        $("#transitions-filter-form").submit(function (e) {
            e.preventDefault();
            return $.fn.loadTimelineList();
        });

        $('#selectUser').select2({
            placeholder: "{{ trans('fi.select_user') }}",
            dropdownAutoWidth: true
        });
        $("#entity-selection").select2({
            placeholder: "{{ trans('fi.select_event') }}",
            dropdownAutoWidth: true
        });

        $('#client-create-note-transition').click(function () {
            $('#note-modal-placeholder').load('{{ route('notes.create') }}');
            $('#client-create-note-transition').addClass('disabled');
        });

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            $.fn.loadTimelineList(page);
        });

        $(document).on('click', '#btn-clear-transition-filter, #reset-transition-btn', function (event) {
            event.preventDefault();

            $('#entity-selection').val([]).trigger('change');
            $('#custom_search').val('');
            $('#selectUser').val([]).trigger('change');

            $('.fetching-records').addClass('fa-spin');
            setTimeout(function () {
                $('.fetching-records').removeClass('fa-spin')
            }, 1500);
        });

        $.fn.loadTimelineList();

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
<div class="transitions-list">
    <div class="card card-solid timeline-container">
        <div class="card-header with-border">
            <h4 class="d-inline">
                <i class="fa fa-clock"></i> {{ trans('fi.timeline') }}
            </h4>
            <span class="badge badge-info transition-count mb-3 va-t"></span>

            <div class="card-tools">
                {!! Form::open(['method' => 'POST', 'url' => route('user.transition.list',['client' => $clientId]), 'id' => 'transitions-filter-form', 'class' => 'form-inline inline m-0']) !!}
                <ul class="nav nav-pills ml-auto mt-1">
                    <li class="nav-item mr-1 mb-1">
                        {!! Form::select('user[]', $filterUsers, null, ['multiple' => 'multiple','id' => 'selectUser','class' => 'form-control form-control-sm mr-1']) !!}
                    </li>
                    <li class="nav-item mr-1 mb-1">
                        {!! Form::select('filter_module[]', $modules, null, ['id' => 'entity-selection','multiple' => 'multiple','class'=>'form-control form-control-sm']) !!}
                    </li>
                    <li class="nav-item mr-1 mb-1">
                        {!! Form::text('custom_search', request('search'), ['id' =>'custom_search', 'class' => 'form-control form-control-sm inline mr-1', 'placeholder' => trans('fi.search')]) !!}
                    </li>
                    <li class="nav-item mb-1 mr-1">
                        <button type="submit" name="search" id="filter-transition-btn"
                                class="btn btn-sm btn-primary" title="{{trans('fi.search')}}">
                            <i class="fa fa-search"></i></button>
                    </li>
                    <li class="nav-item mb-1 mr-1">
                        <button type="button" id="reset-transition-btn" class="btn btn-sm btn-primary"
                                title="{{trans('fi.reset')}}">
                            <i class="fa fa-sync fetching-records" aria-hidden="true"></i>
                        </button>
                    </li>
                    @can('notes.create')
                        <li class="nav-item mb-1 mr-1">
                            <a href="javascript:void(0)" class="btn btn-sm btn-default"
                               id="client-create-note-transition">
                                <i class="fa fa-comments"></i> {{ trans('fi.add_note') }}
                            </a>
                        </li>
                    @endcan
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </ul>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="card-body">
            <div id="timeline-container"></div>
        </div>

    </div>
</div>
