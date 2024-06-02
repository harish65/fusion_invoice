@include('layouts._js_chosen_email')

<script type="text/javascript">

    $(function () {

        var attachPdf = 0;

        $('#modal-mail-quote').modal({backdrop: 'static'}).on('shown.bs.modal', function () {
            chosenEmailField('#to');
            chosenEmailField('#cc');
            chosenEmailField('#bcc');
            chosenEmailField('#mail_from');
        });

        $('#btn-submit-mail-quote').click(function () {

            var $this = $(this);
            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

            if ($('#attach_pdf').prop('checked') == true) {
                attachPdf = 1;
            }

            $.post('{{ route('quoteMail.store') }}', {
                quote_id: '{{ $quoteId }}',
                mail_from: $('#mail_from').val(),
                to: $('#to').val(),
                cc: $('#cc').val(),
                bcc: $('#bcc').val(),
                subject: $('#subject').val(),
                body: $('#body').val(),
                attach_pdf: attachPdf
            }).done(function () {
                $('#modal-mail-quote').modal('hide');

                $this.html($this.data('original-text')).attr("disabled", false);
                window.location.reload();
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });
    });

</script>