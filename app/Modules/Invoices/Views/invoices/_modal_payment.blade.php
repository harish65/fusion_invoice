@include('payments._js_form')
<script type="text/javascript">
    $(function () {
        var modalPayment = $('#modal-payment');

        modalPayment.modal();

        $('#btn-payment-submit').click(function () {
            var custom_fields = {};
            var payment_data = {};
            var files = [];
            var form_data;

            var $this = $(this);
            $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

            $('.custom-form-field').each(function () {
                var fieldName = $(this).data('payments-field-name');
                var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                if (fieldName !== undefined) {
                    if ('file' === inputType) {
                        custom_fields[fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];

                        return true;
                    }

                    if ('select' === inputType) {
                        if ($(this).find('option:selected').length == 0) {
                            custom_fields[fieldName] = '';
                            return true;
                        }
                    }

                    if ('checkbox' === inputType) {
                        custom_fields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                        return true;
                    }

                    if ('radio' === inputType) {
                        if ($(this).prop('checked') == true) {
                            custom_fields[fieldName] = $(this).val();
                        }
                        return custom_fields[fieldName];
                    }


                    custom_fields[fieldName] = $(this).val();
                }
            });

            payment_data['amount'] = $('#amount').val();
            payment_data['paid_at'] = $('#paid_at').children().val();
            payment_data['payment_method_id'] = $('#payment_method_id').val();
            payment_data['note'] = $('#note').val();
            payment_data['invoice_id'] = '{{ $invoice->id }}';
            payment_data['custom'] = custom_fields;
            payment_data['custom_files'] = files;

            form_data = objectToFormData(payment_data);
            $.ajax({
                url: '{{ $submitRoute }}',
                method: 'post',
                data: form_data,
                processData: false,
                contentType: false
            }).done(function (response) {
                modalPayment.modal('hide');
                $('#tab-payments').html(response);
                $('#div-totals').load('{{ route('invoiceEdit.refreshTotals') }}', {
                    id: '{{ $invoice->id }}'
                });
                alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                $this.html($this.data('original-text')).attr("disabled", false);
            }).fail(function (response) {
                $this.html($this.data('original-text')).attr("disabled", false);
                $.each($.parseJSON(response.responseText).errors, function (id, message) {
                    alertify.error(message[0], 5);
                });
            });
        });

        $('#btn-delete-custom-img').click(function () {
            var url = "{{ route('payments.deleteImage', [$payment->id,'field_name' => '']) }}";
            $.post(url + '/' + $(this).data('field-name')).done(function () {
                $('.custom_img').html('');
            });
        });

    });
</script>

<div class="modal" id="modal-payment">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ trans('fi.payment_form') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                {!! Form::model($payment, ['route' => ['payments.update', $payment->id], 'class' => 'form-horizontal']) !!}

                <section class="content">

                    <div class="form-group">
                        <label>{{ trans('fi.amount') }}: </label>
                        {!! Form::text('amount', $payment->formatted_numeric_amount, ['id' => 'amount','class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="input-group date">
                        <label>{{ trans('fi.payment_date') }}: </label>
                        <div class="input-group date" id="paid_at" data-target-input="nearest">

                            {!! Form::text('paid_at', $payment->formatted_paid_at, ['class' => 'custom-form-field form-control form-control-sm', 'data-toggle' => 'datetimepicker', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'autocomplete' => 'off', 'data-label' => $customField->field_label ,'data-target' => "#paid_at"]) !!}
                            <div class="input-group-append"
                                 data-target="#paid_at" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.payment_method') }}</label>
                        {!! Form::select('payment_method_id', $paymentMethods, null, ['id' =>'payment_method_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.note') }}</label>
                        {!! Form::textarea('note', null, ['id' => 'note', 'class' => 'form-control form-control-sm', 'rows'=>'3', 'cols'=>'50']) !!}
                    </div>

                    @if ($customFields)
                        @include('custom_fields._custom_fields_modal', ['object' => isset($payment) ? $payment : []])
                    @endif

                </section>

                {!! Form::hidden('invoice_id') !!}

                {!! Form::close() !!}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-payment-submit" class="btn btn-sm btn-primary"
                        data-loading-text="{{ trans('fi.saving') }}"
                        data-original-text="{{ trans('fi.save') }}">{{ trans('fi.save') }}</button>
            </div>
        </div>
    </div>
</div>