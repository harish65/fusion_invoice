<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-delete').modal();

        $('.btn-{{$modalName}}-cancel').click(function () {
            $('.delete-notes-active').removeClass('delete-notes-active');
        });

        $(document).off("click", "#btn-{{$modalName}}-delete").on("click", "#btn-{{$modalName}}-delete", function () {
            $('#modal-{{$modalName}}-delete').modal('hide');
            showHideLoaderModal();

            var $ele = $('.delete-notes-active');
            let noteId = $ele.data('note-id');
            $('#note-' + noteId).remove();
            $('#note-timeline-item-' + noteId).remove();
            let notesCount = Number($('body .timeline-notes').children().length);
            if (0 < notesCount) {
                $('.notes-count,.note-count').html(Number(notesCount)).show().removeClass('hide');
            } else {
                $('.notes-count,.note-count').html('').hide().addClass('hide');
            }
            $.post("{{ route('notes.delete') }}", {id: noteId})
                .done(function (response) {
                    showHideLoaderModal();
                    $('.delete-{{$modalName}}-active').closest('tr').remove();
                    alertify.success(response.message, 5);
                }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });

            if (typeof $.fn.loadTimelineList == 'function') {
                $.fn.loadTimelineList();
            }

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
                        <button type="button" id="btn-{{$modalName}}-delete"
                                class="btn btn-sm btn-outline-danger float-right ml-2"
                                data-action="{{ $url }}">
                            {{ trans('fi.delete') }}
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