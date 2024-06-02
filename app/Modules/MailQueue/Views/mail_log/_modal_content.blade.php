<script type="text/javascript">
    $(function () {
        $('#modal-mail-content').modal();
    });
</script>

<div class="modal fade" id="modal-mail-content">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $mail->subject }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                {!! $mail->body !!}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.close') }}</button>
            </div>
        </div>
    </div>
</div>