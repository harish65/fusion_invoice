<script type="text/javascript">
    $(window).on('load', function () {
        $('.collapse-toggle-btn').each(function () {
            var widgetName = $(this).data('widget-name');
            if (localStorage.getItem(widgetName) != null) {

                var collapseIconClass = localStorage.getItem(widgetName);

                if (collapseIconClass === 'fas fa-plus') {
                    $('#collapsed-card-' + widgetName).addClass('collapsed-card');
                    $('#collapsed-card-display-' + widgetName).addClass('d-none');
                    $('#collapsed-card-icon-' + widgetName).addClass(collapseIconClass);
                } else {
                    $('#collapsed-card-' + widgetName).removeClass('collapsed-card');
                    $('#collapsed-card-display-' + widgetName).removeClass('d-none').addClass('d-block');
                    $('#collapsed-card-icon-' + widgetName).addClass(collapseIconClass);
                }
            }
        });
    });

    $(document).on('click', '.collapse-toggle-btn', function () {

        var widgetName = $(this).data('widget-name');

        if ($(this).children().attr('class') === 'fas fa-minus') {
            $('#collapsed-card-' + widgetName).removeClass('collapsed-card');
            $('#collapsed-card-display-' + widgetName).removeClass('d-none').addClass('d-block');
        } else {
            $('#collapsed-card-' + widgetName).addClass('collapsed-card');
            $('#collapsed-card-display-' + widgetName).addClass('d-none').removeClass('d-block');
        }
        localStorage.setItem($(this).data('widget-name'), $(this).children().attr('class'));
    });
</script>