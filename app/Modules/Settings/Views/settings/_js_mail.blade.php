@include('layouts._js_chosen_email')

<script type="text/javascript">

    $(function () {

        $('#modal-mail-test').modal({backdrop: 'static'}).on('shown.bs.modal', function () {
            chosenEmailField('#from');
            chosenEmailField('#to');
            chosenEmailField('#cc');
            chosenEmailField('#bcc');
        });

        $('#btn-submit-mail-test').click(function () {

            var $this = $(this);
            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

            $.post('{{ route('testMail.store') }}', {
                from: $('#from').val(),
                to: $.grep($('#to').val(), function (n) {
                    return (n);
                }),
                cc: $('#cc').val(),
                bcc: $('#bcc').val(),
                subject: $('#subject').val(),
                body: $('#body').val()
            }).done(function (response) {
                $('#modal-mail-test').modal('hide');
                if (response.success == true) {
                    alertify.success(response.message);
                } else {
                    $('#attachment-modal-placeholder').load('{!! route('application.clean') !!}', {
                        message: response.message,
                        messageHide: true,
                        action: 'testMail',
                        url: '',
                        delete: '',
                        method: '',
                    });
                }
                $this.html($this.data('original-text')).attr("disabled", false);
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });

    });

</script>