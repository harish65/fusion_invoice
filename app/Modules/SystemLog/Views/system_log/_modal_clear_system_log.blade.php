<script type="text/javascript">
    $(function () {

        $('body #clear-system-log').modal();

        $('#btn-clear-system-log-delete').click(function () {
            $('#clear-system-log').modal('hide');

            showHideLoaderModal();
            $.post('{{route('systemLog.clear')}}')
                .done(function (response) {
                    showHideLoaderModal();
                    alertify.success(response.message, 5);
                    setInterval(function () {
                        window.location.reload();
                    }, 1000)
                }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        });
    });
</script>

<div class="modal fade" id="clear-system-log" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"> {{trans('fi.delete-confirm')}} </h5>
                <button type="button" class="close btn-clear-system-log-cancel" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! trans('fi.system_log_clear_message') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-1 pt-1">
                <div class="col-sm-12">
                    <button type="button" id="btn-clear-system-log-delete"
                            class="btn btn-sm btn-outline-danger float-right ml-2">
                        {{ trans('fi.clear') }}
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-outline-secondary float-right btn-clear-system-log-cancel"
                            data-dismiss="modal" id="btn-clear-system-log-cancel">
                        {{ trans('fi.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>