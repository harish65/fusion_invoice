<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-delete').modal();

        $('#btn-{{$modalName}}-cancel').click(function () {
            $('.delete-{{$modalName}}-active').removeClass('delete-{{$modalName}}-active');
        });

        $('#btn-{{$modalName}}-delete').click(function () {
            $('#modal-{{$modalName}}-delete').modal('hide');

            showHideLoaderModal();
            $.post($(this).data('action'), {
                ids: $.parseJSON('{!! $data !!}'),
            }).done(function (response) {
                showHideLoaderModal();
                setInterval(function () {
                    window.location.replace('{{ $returnURL }}');
                }, 1000);
                alertify.success(response.message, 5);
            }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
                setInterval(function () {
                    window.location.replace('{{ $returnURL }}');
                }, 2000);
            });
        });
    });
</script>

<div class="modal fade" id="modal-{{$modalName}}-delete" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"> {{trans('fi.delete-confirm')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! trans('fi.bulk_delete_record_warning') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-1 pt-1">
                <div class="col-sm-12">
                    <button type="button" id="btn-{{$modalName}}-delete"
                            class="btn btn-sm btn-outline-danger float-right ml-2"
                            data-action="{{ $url }}">
                        {{ trans('fi.delete') }}
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-secondary float-right" data-dismiss="modal"
                            id="btn-{{$modalName}}-cancel">
                        {{ trans('fi.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>