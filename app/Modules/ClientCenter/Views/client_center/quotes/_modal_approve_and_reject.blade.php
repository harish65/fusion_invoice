<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-approve-and-reject').modal();

        $('.btn-{{$modalName}}-cancel').click(function () {
            $('.quote-disabled').removeClass('disabled');
        });

        $('#btn-{{$modalName}}-approve-and-reject').click(function () {
            $('#modal-loading').modal();
            $('#modal-{{$modalName}}-approve-and-reject').modal('hide');
            var $_this = $(this);
            $_this.addClass('disabled');
            $.get($_this.data('action'))
                .done(function (response) {
                    $('#modal-loading').modal('hide');
                    window.location.reload();
                }).fail(function (response) {
                $('#modal-loading').modal('hide');
                window.location.reload();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        });
    });
</script>

<div class="modal fade" id="modal-{{$modalName}}-approve-and-reject" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"> {{trans('fi.delete-confirm')}} </h5>
                <button type="button" class="close btn-{{$modalName}}-cancel" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <form id="columns-filter">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                {!! $message !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pb-1 pt-1">
                    <div class="col-sm-12">
                        <button type="button" id="btn-{{$modalName}}-approve-and-reject"
                                class="btn btn-sm btn-outline-danger float-right ml-2"
                                data-action="{{ $url }}">
                            {{ trans('fi.ok') }}
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-outline-secondary float-right btn-{{$modalName}}-cancel"
                                data-dismiss="modal"
                                id="btn-{{$modalName}}-cancel">
                            {{ trans('fi.cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal align-middle" id="modal-loading" data-keyboard="false" data-backdrop="static" aria-modal="true"
     role="dialog">
    <div class="modal-dialog modal-dialog-centered justify-content-center">
        <div class="task-list-container-loader">
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only"> {{ trans('fi.loading') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>