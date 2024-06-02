<script type="text/javascript">

    $(function () {

        $('#modal-copy-invoice').modal();

        $('#modal-copy-invoice').on('shown.bs.modal', function () {
            $("#copy_client_name").focus();
        });

        var settings = {
            placeholder: '{{ trans('fi.select_client') }}',
            allowClear: true,
            language: {
                noResults: function () {
                    return '<li><a href="javascript:void(0)" class="text-primary create-client btn-sm"><i class="fa fa-plus"></i> {{ trans('fi.add-new-client') }}</a></li>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        };
        $('#copy_client_name').select2(settings);

        $("#copy_invoice_date").datetimepicker({format: dateFormat, defaultDate: new Date(), autoclose: true});

        // Creates the invoice
        $('#btn-copy-invoice-submit').click(function () {
            $.post('{{ route('invoiceCopy.store') }}', {
                invoice_id: '{{ $invoice->id }}',
                client_id: $('#copy_client_name').val(),
                company_profile_id: $('#copy_company_profile_id').val(),
                invoice_date: $('#copy_invoice_date').children().val(),
                document_number_scheme_id: $('#copy_document_number_scheme_id').val(),
                user_id: '{{ $user_id }}',
                type: '{{ $invoice->type }}'
            }).done(function (response) {
                window.location = '{{ url('invoices') }}' + '/' + response.id + '/edit';
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });

        $(document).off('click', ".create-client").on("click", ".create-client", function () {
            var select2_value = $(this).closest('.select2-results').prev().children().val();
            var client_name = (select2_value != null && select2_value != 'undefined') ? select2_value : null;

            $('#modal-invoice-client-copy').load('{!! route('invoices.client.create.modal') !!}', {
                client_name: client_name,
                type: 'copy'
            })
        });
    });

</script>