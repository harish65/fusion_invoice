@include('layouts._js_chosen_email')

<script type="text/javascript">

    $(function () {

        $('#modal-mail-payment').modal({backdrop: 'static'}).on('shown.bs.modal', function () {
            chosenEmailField('#to');
            chosenEmailField('#cc');
            chosenEmailField('#bcc');
            chosenEmailField('#mail_from');
        });

        $('#btn-submit-mail-payment').click(function () {

            var $this = $(this);
            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

            $.post('{{ route('paymentMail.store') }}', {
                payment_id: '{{ $paymentId }}',
                mail_from: $('#mail_from').val(),
                to: $('#to').val(),
                cc: $('#cc').val(),
                bcc: $('#bcc').val(),
                subject: $('#subject').val(),
                body: $('#body').val(),
                attach_pdf: $('#attach_pdf').prop('checked') == true ? 1 : 0,
            }).done(function () {
                $('#modal-status-placeholder').html('<div class="alert alert-success">' + '{{ trans('fi.payment_receipt_email_sent') }}' + '</div>');
                setTimeout('window.location=\'{{ $redirectTo }}\'', 1000);
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });
    });

</script>