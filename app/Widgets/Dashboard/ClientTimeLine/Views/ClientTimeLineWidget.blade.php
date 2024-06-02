@can('client_timeline.view')
    <div id="client_timeline-widget">
        @include('layouts._select2')
        @include('layouts._bootstrap-multiselect')
        <script type="text/javascript">

            $(function () {
                if ($('#entity-selection').length == 0) {
                    loadTimelineList();
                }
                $(document).ready(function () {
                    if ($('#custom_search').val() == '') {
                        $('#custom_search').val($('#cookie_time_line_search').val());
                    }

                    $("#selectUser").select2({
                        placeholder: "{{ trans('fi.select_user') }}",
                        dropdownAutoWidth: true
                    }).val($.parseJSON($('#cookie_value').val()));

                    $("#entity-selection").select2({
                        placeholder: "{{ trans('fi.select_event') }}",
                        dropdownAutoWidth: true
                    }).val(($('#cookie_event_type').val()).split(','));

                    if ($('#cookie_event_type').val() != null || $('#cookie_value').val() != null) {
                        $("#selectUser").trigger('change');
                    }
                });

                function loadTimelineList(page = 1) {
                    let $form = $('#transitions-filter-form');
                    let data = $form.serializeArray();

                    data.push({name: 'page', value: page});

                    $.ajax({
                        url: "{{route('transitions.widget.list')}}",
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

                $('#selectUser,#entity-selection').on('change', function () {
                    return loadTimelineList();
                });

                $("#transitions-filter-form").submit(function (e) {
                    e.preventDefault();
                    return loadTimelineList();
                });

                $('#selectUser').select2({
                    placeholder: "{{ trans('fi.select_user') }}",
                    dropdownAutoWidth: true
                });
                $("#entity-selection").select2({
                    placeholder: "{{ trans('fi.select_event') }}",
                    dropdownAutoWidth: true
                });


                $('#reset-transition-btn').on('click', function () {

                    $('#cookie_event_type ,#cookie_value ,#cookie_time_line_search ,#custom_search').val('');

                    $.ajax({
                        url: '{{ route('transitions.widget.refresh') }}',
                        method: 'post',
                        beforeSend: function () {
                            showHideLoaderModal();
                        },
                        success: function () {
                            showHideLoaderModal();
                            $('#custom_search').val('');
                            loadTimelineList();
                        }
                    });

                    $('#selectUser').select2('destroy').val("").select2({
                        placeholder: '{{trans('fi.select_users')}}'
                    });

                    $('#entity-selection').select2('destroy').val("").select2({
                        placeholder: '{{trans('fi.select_event')}}'
                    });

                    $('.fetching-records').addClass('fa-spin');

                    setTimeout(function () {
                        $('.fetching-records').removeClass('fa-spin')
                    }, 1500);
                });

                $(document).on('click', '.transitions-pages > nav > .pagination a', function (event) {
                    event.preventDefault();
                    loadTimelineList($(this).attr('href').split('page=')[1]);
                });
            });
        </script>

        <div class="card card-primary card-outline timeline-container " id="collapsed-card-client-time-line">
            <input type="hidden" value="{{ \Cookie::get('eventType')}}" id="cookie_event_type">
            <input type="hidden" value="[{{ \Cookie::get('userIds')}}]" id="cookie_value">
            <input type="hidden" value="{{ \Cookie::get('time_line_search')}}" id="cookie_time_line_search">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-clock"></i> {{ trans('fi.timeline') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool collapse-toggle-btn "
                            data-widget-name='client-time-line' data-card-widget="collapse">
                        <i class="fas fa-minus" id="collapsed-card-icon-client-time-line"></i>
                    </button>
                </div>

            </div>
            <div class="card-body " id="collapsed-card-display-client-time-line">

                <div class="row pl-2 flex-row-reverse">
                    <div class="float-right">
                        @if(!empty($filterUsers))
                            {!! Form::open(['method' => 'GET', 'id' => 'transitions-filter-form', 'class' => 'form-inline inline m-0']) !!}

                            <ul class="nav nav-pills ml-auto">

                                <li class="nav-item mt-1 mb-1 mr-1">
                                    {!! Form::select('user[]', $filterUsers, null, ['multiple' => 'multiple','id' => 'selectUser','class' => 'form-control form-control-sm ', 'style'=>"width: 200px;"]) !!}
                                </li>

                                <li class="nav-item mt-1 mb-1 mr-1 entity-selection-btn">
                                    {!! Form::select('filter_module[]', $modules, null, ['id' => 'entity-selection','multiple' => 'multiple','class' => 'form-control form-control-sm mr-1 ml-1']) !!}
                                </li>
                                <li class="nav-item mb-1 mr-1">
                                    <div class="input-group input-group-sm mt-1">
                                        {!! Form::text('custom_search', request('search'), ['id' =>'custom_search', 'class' => 'h-auto form-control form-control-sm inline mr-1', 'placeholder' => trans('fi.search')]) !!}
                                        <button type="submit" name="search" id="filter-transition-btn"
                                                class="btn btn-sm btn-primary mr-1" title="{{trans('fi.search')}}">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </li>

                                <li class="nav-item mt-1 mb-1 mr-1">
                                    <button type="button" id="reset-transition-btn" class="btn btn-sm btn-primary"
                                            title="{{trans('fi.reset')}}"><i class="fa fa-sync fetching-records"></i>
                                    </button>
                                </li>

                            </ul>
                            {!! Form::close() !!}
                        @endif
                    </div>
                </div>
                <div id="timeline-container"></div>
            </div>
        </div>
    </div>

@endcan