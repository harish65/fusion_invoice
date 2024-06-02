<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-delete').modal();

        $('.btn-{{$modalName}}-cancel').click(function () {
            $('.delete-{{$modalName}}-active').removeClass('delete-{{$modalName}}-active');
        });

        $('#btn-{{$modalName}}-delete').click(function () {
            $('#modal-{{$modalName}}-delete').modal('hide');
            showHideLoaderModal();
            @if ($index > 0)
            showHideLoaderModal();
            window.location.href = $(this).data('action');
            @else
            $.get($(this).data('action'))
                .done(function (response) {
                    showHideLoaderModal();
                    let count = parseInt($('.commissions-count').html());

                    if (count > 0) {
                        $('.commissions-count').html(count - 1);
                    }
                    $('.delete-{{$modalName}}-active').closest('tr').remove();
                    loadInvoiceCommission();
                    alertify.success(response.message, 5);
                }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
            @endif
        });
    });
</script>

<div class="modal fade" id="modal-{{$modalName}}-delete" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"> {{trans('fi.delete-confirm')}} </h5>
                <button type="button" class="close btn-{{$modalName}}-cancel" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
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
                    <button type="button" id="btn-{{$modalName}}-delete"
                            class="btn btn-sm btn-outline-danger float-right ml-2"
                            data-action="{{ $action }}">
                        {{ trans('fi.delete') }}
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-secondary float-right btn-{{$modalName}}-cancel"
                            data-dismiss="modal"
                            id="btn-{{$modalName}}-cancel">
                        {{ trans('fi.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>