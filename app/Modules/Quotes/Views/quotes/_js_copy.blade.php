<script type="text/javascript">

    $(function () {

        $('#modal-copy-quote').modal();

        $('#modal-copy-quote').on('shown.bs.modal', function () {
            $("#client_name").focus();
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

        $("#copy_quote_date").datetimepicker({format:dateFormat,defaultDate:new Date(),autoclose: true});

        // Creates the quote
        $('#btn-copy-quote-submit').click(function () {
            $.post('{{ route('quoteCopy.store') }}', {
                quote_id: '{{ $quote->id }}',
                client_id: $('#copy_client_name').val(),
                company_profile_id: $('#copy_company_profile_id').val(),
                quote_date: $('#copy_quote_date').children().val(),
                document_number_scheme_id: $('#copy_document_number_scheme_id').val(),
                user_id: '{{ $user_id }}'
            }).done(function (response) {
                window.location = '{{ url('quotes') }}' + '/' + response.id + '/edit';
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });

        $(document).off('click',".create-client").on("click", ".create-client", function () {

            var client_name = ($('.select2-search__field').val() != null && $('.select2-search__field').val() != 'undefined') ? $('.select2-search__field').val() : null;

            $('#modal-quote-client-copy').load('{!! route('quotes.client.create.modal') !!}', {
                client_name: client_name,
                type:'copy'
            })
        });
    });

</script>