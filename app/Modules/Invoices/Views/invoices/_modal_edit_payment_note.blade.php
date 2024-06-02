@include('payments._js_form')
<script type="text/javascript">
    $(function () {
        var modalPayment = $('#modal-payment');

        modalPayment.modal();

        $('#btn-edit-payment-note-submit').click(function () {
            var payment_data = {};
            var form_data;
            var $this = $(this);

            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);
            payment_data['note'] = $('#note').val();
            payment_data['invoice_id'] = '{{ $invoice->id }}';
            form_data = objectToFormData(payment_data);

            $.ajax({
                url: '{{ $submitRoute }}',
                method: 'post',
                data: form_data,
                processData: false,
                contentType: false
            }).done(function (response) {
                alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                $this.html($this.data('original-text')).attr("disabled", false);

                if (response.success == true) {
                    location.reload();
                } else {
                    showAlertifyErrors($.parseJSON(response.responseText).message);
                }
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                $.each($.parseJSON(response.responseText).errors, function (id, message) {
                    alertify.error(message[0], 5);
                });
            });
        });
    });
</script>

<div class="modal" id="modal-payment">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ trans('fi.edit_payment_note_form') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <section class="content">
                    {!! Form::model($payment, ['route' => ['payments.note.update', $payment->id], 'class' => 'form-horizontal']) !!}
                    <div class="form-group">
                        <label>{{ trans('fi.note') }}</label>
                        {!! Form::textarea('note', null, ['id' => 'note', 'class' => 'form-control form-control-sm', 'rows'=>'3', 'cols'=>'50']) !!}
                    </div>
                    {!! Form::hidden('invoice_id',$invoice->id) !!}
                    {!! Form::close() !!}
                </section>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-edit-payment-note-submit" class="btn btn-sm btn-primary"
                        data-loading-text="{{ trans('fi.saving') }}"
                        data-original-text="{{ trans('fi.save') }}">{{ trans('fi.save') }}</button>
            </div>
        </div>
    </div>
</div>