@include('layouts._js_chosen_email')

<script type="text/javascript">

    $(function () {

        $('#modal-mail-invoice').modal({backdrop: 'static'}).on('shown.bs.modal', function () {
            chosenEmailField('#to');
            chosenEmailField('#cc');
            chosenEmailField('#bcc');
            chosenEmailField('#mail_from');
        });

        $('#btn-submit-mail-invoice').click(function () {

            var $this = $(this);
            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

            $.post('{{ route('invoiceMail.store') }}', {
                invoice_id: '{{ $invoice->id }}',
                mail_from: $('#mail_from').val(),
                to: $('#to').val(),
                cc: $('#cc').val(),
                bcc: $('#bcc').val(),
                subject: $('#subject').val(),
                body: $('#body').val(),
                attach_pdf: $('#attach_pdf').prop('checked') == true ? 1 : 0
            }).done(function () {
                $('#modal-mail-invoice').modal('hide');

                alertify.success('{{ trans('fi.email_sent') }}');
                setTimeout(function () {
                    window.location.reload();
                }, 3000);

                $('#div-invoice-edit').load('{{ route('invoiceEdit.refreshEdit', [$invoice->id]) }}', function () {
                    alertify.success('{{ trans('fi.email_sent') }}', 5);
                    var settings = {
                        placeholder: '{{ trans('fi.select-item') }}',
                        allowClear: true,
                        tags: true,
                        selectOnClose: true
                    };
                    // Make all existing items select
                    $('.item-lookup').select2(settings);
                });
                $this.html($this.data('original-text')).attr("disabled", false);
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });

    });

</script>
