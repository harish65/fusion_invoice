<script type="text/javascript">
    $(function () {
        // Define the select settings

        @if(isset($addNew) && $addNew == true)
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
        @else
            var settings = {
                placeholder: '{{ trans('fi.select_client') }}',
                allowClear: true,
                escapeMarkup: function (markup) {
                    return markup;
                }
            };
        @endif

        // Make all existing items select
        var client_lookup = $('.client-lookup').select2(settings);
    });
</script>