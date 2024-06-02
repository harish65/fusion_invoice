<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-delete').modal();

        $('.btn-{{$modalName}}-cancel').click(function () {
            $('.delete-{{$modalName}}-active').removeClass('delete-{{$modalName}}-active');
        });

        $('#btn-{{$modalName}}-delete').click(function () {
            $('#modal-{{$modalName}}-delete').modal('hide');

            showHideLoaderModal();

            $.post("{{ route('attachments.ajax.delete') }}", {
                model: '{{ addslashes($model) }}',
                model_id: '{{ $modelId }}',
                attachment_id: {{ $attachmentId }},
                client_id: '{{ $clientId }}'
            }, function () {
                $('#attachments-list').load("{{ route('attachments.ajax.list') }}", {
                    model: '{{ addslashes($model) }}',
                    model_id: '{{ $modelId }}'
                }, function () {
                    let attachmentCount = Number($('#attachments-list table tr').length) - 1;
                    if (0 < attachmentCount) {
                        $('.attachment-count').html(Number(attachmentCount)).show().removeClass('hide');
                    } else {
                        $('.attachment-count').html('').hide().addClass('hide');
                    }
                });
            }).done(function (response) {
                showHideLoaderModal();
                $('.delete-{{$modalName}}-active').closest('tr').remove();
                alertify.success(response.message, 5);
            }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });


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
                            {!! trans('fi.delete_record_warning') !!}
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

                    <button type="button" class="btn btn-sm btn-outline-secondary float-right btn-{{$modalName}}-cancel"
                            data-dismiss="modal" id="btn-{{$modalName}}-cancel">
                        {{ trans('fi.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>