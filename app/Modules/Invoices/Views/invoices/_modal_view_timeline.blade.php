@include('layouts._select2')
@include('layouts._bootstrap-multiselect')
<script type="text/javascript">
    $(function () {

        $.fn.loadTimelineList = function (page = 1) {
            let $form = $('#transitions-filter-form');
            let data = $form.serializeArray();
            data.push({name: 'page', value: page});
            let passUrl = "{{route('invoice.transition.list',['invoice' => $invoiceId])}}";
            $.ajax({
                url: passUrl,
                method: 'post',
                data: data,
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function (response) {
                    showHideLoaderModal();
                    $('#timeline-container-list').html(response);
                },
                error: function () {
                    showHideLoaderModal();
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
        };

        $('#selectUser').on('change', function () {
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

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            $.fn.loadTimelineList(page);
        });

        $(document).on('click', '#btn-clear-transition-filter, #reset-transition-btn', function (event) {
            event.preventDefault();
            $('#custom_search').val('');
            $('#selectUser').val([]).trigger('change');

            $('.fetching-records').addClass('fa-spin');
            setTimeout(function () {
                $('.fetching-records').removeClass('fa-spin')
            }, 1500);
        });
        $('body #modal-invoice-timeline').modal();
        $.fn.loadTimelineList();
    });
</script>
<div class="modal fade" id="modal-invoice-timeline" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-clock"></i> {{trans('fi.invoice')}} {{$invoiceNumber}} {{trans('fi.timeline')}}</h5>   
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body pb-0 pt-1">

                <div class="card-body p-0">
                    {!! Form::open(['method' => 'GET', 'url' => route('invoice.transition.list',['invoice' => $invoiceId]), 'id' => 'transitions-filter-form', 'class' => 'form-inline inline m-0']) !!}
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <div class="input-group mt-1 mb-1 mr-1">
                                {!! Form::text('custom_search', request('search'), ['id' =>'custom_search', 'class' => 'form-control form-control-sm inline mr-1', 'placeholder' => trans('fi.search')]) !!}
                                <button type="submit" name="search" id="filter-transition-btn" class="btn btn-sm btn-primary" title="{{trans('fi.search')}}">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('user[]', $filterUsersData, null, ['multiple' => 'multiple','id' => 'selectUser','class' => 'form-control form-control-sm mr-1']) !!}
                        </li>
                        <li class="nav-item mt-1 mb-1 ">
                            <div class="form-group">
                                <button type="button" id="reset-transition-btn" class="btn btn-sm btn-primary" title="{{trans('fi.reset')}}">
                                    <i class="fa fa-sync fetching-records" aria-hidden="true"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                    {!! Form::close() !!}
                    <div id="timeline-container-list"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
            </div>
        </div>
    </div>
</div>