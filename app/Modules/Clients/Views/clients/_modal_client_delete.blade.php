<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-delete').modal();

        $('#btn-{{$modalName}}-cancel').click(function () {
            $('.delete-{{$modalName}}-active').removeClass('delete-{{$modalName}}-active');
        });

        $('#btn-{{$modalName}}-delete').click(function () {
            $('#modal-{{$modalName}}-delete').modal('hide');

            showHideLoaderModal();
            $.get($(this).data('action'))
                .done(function (response) {
                    if (response.success == true) {
                        showHideLoaderModal();
                        alertify.success('{{ trans('fi.record_successfully_deleted') }}', 5);
                        @if($isReload == 'true')
                        window.location.replace('{{ $returnURL }}');
                        @else
                        $('.delete-{{$modalName}}-active').closest('tr').remove();
                        @endif
                    }
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


<div class="modal fade" id="modal-{{$modalName}}-delete" data-keyboard="false" data-backdrop="static">
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
                                {!! trans('fi.delete_client_warning') !!}
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
            </form>
        </div>
    </div>
</div>