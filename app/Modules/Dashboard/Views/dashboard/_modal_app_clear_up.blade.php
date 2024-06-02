<script type="text/javascript">
    $(function () {

        $('body #modal-' + '{{$action}}').modal();

        $('#btn-submit-{{$action}}').click(function (evt) {
            var $this = $(this);
            $('.close-hide').attr("disabled", true);
            $this.html('<i class="fa fa-spinner fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);
            var url = $(this).data('url');
            var method = '{{$method}}';
            @if('get' == $method)
            $.get(url).done(function (response) {
                $('.close-hide').attr("disabled", false);
                $('#modal-{{$action}}').modal('hide');
                alertify.success(response.message, 5);
                $this.html($this.data('original-text')).attr("disabled", false);
            }).fail(function (response) {
                $('#modal-{{$action}}').modal('hide');
                $('.close-hide').attr("disabled", false);
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
            @else
            $.post(url).done(function (response) {
                $('.close-hide').attr("disabled", false);
                $('#modal-{{$action}}').modal('hide');
                alertify.success(response.message, 5);
                $this.html($this.data('original-text')).attr("disabled", false);
            }).fail(function (response) {
                $('.close-hide').attr("disabled", false);
                $('#modal-{{$action}}').modal('hide');
                $this.html($this.data('original-text')).attr("disabled", false);
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
            @endif
        });

    });
</script>

<div class="modal fade" id="modal-{{$action}}" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"> {{trans('fi.delete-confirm')}} </h5>
                <button type="button" class="close close-hide" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {{$message}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer pb-1 pt-1">
                <div class="col-sm-12">
                    @if(isset($messageHide) && $messageHide == true)
                        <button type="button" class="btn btn-sm btn-outline-secondary float-right"
                                data-dismiss="modal"
                                id="btn-clear-testMail">
                            {{ trans('fi.ok') }}
                        </button>
                    @else
                        <button type="button" id="btn-submit-{{$action}}"
                                class="btn btn-sm btn-outline-danger float-right ml-2"
                                data-original-text="{{ (isset($delete ) && $delete  != null ) ? $delete : trans('fi.delete') }}"
                                data-loading-text="{{ (isset($delete ) && $delete  != null ) ? $delete : trans('fi.delete') }}"
                                data-url="{{$url}}">
                            {{ (isset($delete ) && $delete  != null ) ? $delete : trans('fi.delete') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary float-right close-hide"
                                data-dismiss="modal"
                                id="btn-clear-column-filter">
                            {{ trans('fi.cancel') }}
                        </button>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>