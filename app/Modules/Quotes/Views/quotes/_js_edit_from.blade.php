<script type="text/javascript">
    $(function () {
        $('#btn-change-company_profile').click(function () {
            $('#modal-placeholder').load('{{ route('company.profiles.ajax.modalLookup') }}', {
                id: '{{ $quote->id }}',
                update_company_profile_route: '{{ route('quoteEdit.updateCompanyProfile') }}',
                refresh_from_route: '{{ route('quoteEdit.refreshFrom') }}'
            });
        });
    });
</script>