<script type="text/javascript">
    $(function () {

        $(document).ready(function () {
            @if(config('fi.floatingTimeTrackingAddon') == 1)
            $('#enable-disable-task-checkbox').prop('checked', true);
            @endif
        });
        $('.btn-enable-task-popup-cancel').click(function (){
            $('.time-tracker-popup-modal').removeClass('disabled');
        });
        $('body #modal-enable-task-popup').modal();

        $('#enable-disable-task-checkbox').change(function () {

            var enableDisable = (this.checked) ? 1 : 0;

            showHideLoaderModal();

            $.post('{{route('timeTracking.timers.enable.disable')}}', {
                flag: enableDisable
            }).done(function (response) {
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

<div class="modal fade" id="modal-enable-task-popup" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{trans('fi.settings')}} </h5>
                <button type="button" class="close btn-enable-task-popup-cancel" data-dismiss="modal"
                        aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">

                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input" id="enable-disable-task-checkbox">
                                <label class="custom-control-label" for="enable-disable-task-checkbox">
                                    {{trans('TimeTracking::lang.floating_time_tracking_addon')}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>