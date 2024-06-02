<script type="text/javascript">
    $(function () {

        $('body #modal-task-delete').modal();

        $('#btn-task-cancel').click(function () {
            $('.delete-task-active').removeClass('delete-task-active');
        });

        $('#btn-task-delete').click(function () {
            $('#modal-task-delete').modal('hide');
            showHideLoaderModal();

            var $_this = $(this);
            let url = $_this.data('action');
            var tab = $_this.data('tab');
            var returnURL = '{{$returnURL}}';

            $.get(url).done(function () {
                alertify.success('{{ trans('fi.record_successfully_deleted') }}', 5);
                showHideLoaderModal();
                @if($isReload == 'true')
                    window.location.replace('{{ route('task.index') }}');
                @else
                    @if($widgetTask == 'false')
                        $('.delete-task-active').closest('.task-complete').remove();
                    @endif
                    @if($isReload == 'false')
                        $('.delete-task-active').closest('tr').remove();
                        if (tab) {
                            var url = new URL(returnURL);
                            url.searchParams.set("tab", tab);
                            window.location.replace(url.href);
                        } else {
                        }
                    @endif
                @endif
            }).fail(function (response) {
                showHideLoaderModal();

                if (response.status == 400) {
                    showAlertifyErrors($.parseJSON(response.responseText).errors);
                } else {
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
        });
    });
</script>


<div class="modal fade" id="modal-task-delete" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"> {{trans('fi.delete-confirm')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form id="columns-filter">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                {!! trans('fi.delete_record_warning') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer pb-1 pt-1">
                    <div class="col-sm-12">
                        <button type="button" id="btn-task-delete"
                                class="btn btn-sm btn-outline-danger float-right ml-2"
                                data-action="{{$url}}" data-tab="{{ $tab }}">
                            {{ trans('fi.ok') }}
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary float-right" data-dismiss="modal"
                                id="btn-task-cancel">
                            {{ trans('fi.cancel') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>