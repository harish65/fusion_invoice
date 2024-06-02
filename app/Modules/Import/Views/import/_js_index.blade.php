<script type="text/javascript">

    $(function () {

        @if(isset($importType))
            var importType = '{{$importType}}';
            let importExampleUrl = '{{ route("import.example", ":import_type") }}';
            importExampleUrl = importExampleUrl.replace(':import_type', importType);
            $('#example_import_link').attr('data-href', importExampleUrl);
            $('#example_import_link').html(importType + '_import.csv');
        @endif

        $(".import_files").change(function () {
            var file = $(this)[0].files[0];
            if (file) {
                $('.import_next_btn').removeClass('d-none').addClass('d-block');
            } else {
                alertify.error('{!! trans('fi.no_file_selected') !!}', 5);
            }
        });

        $('#import_type').change(function () {
            let module = $(this).val();
            let importExampleUrl = '{{ route("import.example", ":import_type") }}';
            importExampleUrl = importExampleUrl.replace(':import_type', module);
            $('#example_import_link').attr('data-href', importExampleUrl);
            $('#example_import_link').html(module + '_import.csv');
        });

        $(document).on('click', '#example_import_link', function () {
            var $_this = $(this);
            window.open($_this.attr('data-href'), '_blank');
        });
    });

</script>