<script type="text/javascript">

    $(function () {

        $('#create-quote').modal();

        $('#create-quote').on('shown.bs.modal', function () {
            $("#create_client_name").focus();
        });

        $('.modal-create-quote-close').click(function () {
            $('.btn-action-modal').removeClass('disabled');
        });

        $("#create_quote_date").datetimepicker({autoclose: true, format: dateFormat, defaultDate: new Date()});

        @can('quotes.create')
        $('#quote-create-confirm').click(function () {

            $.post('{{ route('quotes.store') }}', {
                user_id: $('#user_id').val(),
                company_profile_id: $('#company_profile_id').val(),
                client_id: $('#create_client_name').val(),
                quote_date: $('#create_quote_date').children().val(),
                document_number_scheme_id: $('#create_document_number_scheme_id').val()
            }).done(function (response) {
                window.location = '{{ url('quotes') }}' + '/' + response.id + '/edit';
            }).fail(function (response) {
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });
        });

        $(document).off('click', ".create-client").on("click", ".create-client", function () {

            var client_name = ($('.select2-search__field').val() != null && $('.select2-search__field').val() != 'undefined') ? $('.select2-search__field').val() : null;

            $('#modal-quote-client-create').load('{!! route('quotes.client.create.modal') !!}', {
                client_name: client_name,
                type:'create'
            })
        });

        @endcan
    });
</script>