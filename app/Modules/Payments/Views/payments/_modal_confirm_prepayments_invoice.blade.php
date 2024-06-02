<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-delete').modal();

        $('#btn-{{$modalName}}-cancel').click(function () {
            $('.delete-{{$modalName}}-active').removeClass('delete-{{$modalName}}-active');
        });

        $('#btn-{{$modalName}}-delete').click(function () {
            $('#modal-{{$modalName}}-delete').modal('hide');

            showHideLoaderModal();

            @if($flag == 'true')
            $('#confirm-payment-invoices').prop("disabled", true).html($('#confirm-payment-invoices').data('loading-text'));

            $.post($(this).data('action'), $('#fetch-invoices-form').serialize())
                .done(function (response) {
                    showHideLoaderModal();

                    if (response.success == true) {
                        $("#modal-fetch-invoices").modal("hide");
                        location.reload();
                    } else {
                        showAlertifyErrors($.parseJSON(response.responseText).message);
                    }
                }).fail(function (response) {
                showHideLoaderModal();
                showAlertifyErrors($.parseJSON(response.responseText).message);
            });
            @else
            showHideLoaderModal();
            @endif
        });
    });
</script>

<div class="modal fade" id="modal-{{$modalName}}-delete" data-keyboard="false" data-backdrop="static"
     style="z-index: 99999; position: absolute; display: block;">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header bg-{{$color}}">
                <h5 class="modal-title"> {!! $header !!} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form id="columns-filter">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                {!! $confirmRemainingBalance !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pb-1 pt-1">
                    <div class="col-sm-12">
                        <button type="button" id="btn-{{$modalName}}-delete"
                                class="btn btn-sm btn-outline-{{$color}} float-right ml-2"
                                data-action="{{ $url }}">
                            {{ trans('fi.ok') }}
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary float-right" data-dismiss="modal"
                                id="btn-{{$modalName}}-cancel">
                            {{ trans('fi.cancel') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>