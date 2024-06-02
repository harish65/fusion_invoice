<script src='{{ asset('assets/plugins/daterangepicker/moment.js?v='.config('fi.version')) }}'></script>
<script src='{{ asset('assets/plugins/daterangepicker/daterangepicker.js?v='.config('fi.version')) }}'></script>
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css?v='.config('fi.version')) }}" rel="stylesheet" type="text/css"/>
<style>
    .daterangepicker .ranges ul {
        list-style: none;
        margin: 0 auto;
        padding: 0;
        height: 250px;
        overflow: auto;
        width: 100%;
    }

    .ranges > ul::-webkit-scrollbar {
        width: 6px;
    }

    .ranges > ul::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background-color: #fff;
        -webkit-box-shadow: inset 0 0 6px rgb(206 212 218);
    }
</style>
<script type="text/javascript">

    function initDateRangePicker(identifier) {

        var startDate = '{{ request('from_date','') }}';

        var endDate = '{{ request('to_date','') }}';

        $('#' + identifier + '_date_range').daterangepicker({
            startDate: startDate != '' ? moment(startDate) : moment(),
            endDate: endDate != '' ? moment(endDate) : moment(),
            autoUpdateInput: false,
            locale: {
                cancelLabel: '{{ trans('fi.cancel') }}',
                format: "{{ strtoupper(config('fi.datepickerFormat')) }}",
                customRangeLabel: "{!! trans('fi.custom') !!}",
                daysOfWeek: [
                    "{{ trans('fi.day_short_sunday') }}",
                    "{{ trans('fi.day_short_monday') }}",
                    "{{ trans('fi.day_short_tuesday') }}",
                    "{{ trans('fi.day_short_wednesday') }}",
                    "{{ trans('fi.day_short_thursday') }}",
                    "{{ trans('fi.day_short_friday') }}",
                    "{{ trans('fi.day_short_saturday') }}"
                ],
                monthNames: [
                    "{{ trans('fi.month_january') }}",
                    "{{ trans('fi.month_february') }}",
                    "{{ trans('fi.month_march') }}",
                    "{{ trans('fi.month_april') }}",
                    "{{ trans('fi.month_may') }}",
                    "{{ trans('fi.month_june') }}",
                    "{{ trans('fi.month_july') }}",
                    "{{ trans('fi.month_august') }}",
                    "{{ trans('fi.month_september') }}",
                    "{{ trans('fi.month_october') }}",
                    "{{ trans('fi.month_november') }}",
                    "{{ trans('fi.month_december') }}"
                ],
                firstDay: 1
            },
            ranges: {
                '{{ trans('fi.today') }}': [moment(), moment()],
                '{{ trans('fi.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ trans('fi.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
                '{{ trans('fi.last_30_days') }}': [moment().subtract(29, 'days'), moment()],
                '{{ trans('fi.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ trans('fi.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                '{{ trans('fi.this_year') }}': [moment().startOf('year'), moment().endOf('year')],
                '{{ trans('fi.last_year') }}': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                '{{ trans('fi.this_quarter') }}': [moment().startOf('quarter'), moment().endOf('quarter')],
                '{{ trans('fi.last_quarter') }}': [moment().subtract(1, 'quarter').startOf('quarter'), moment().subtract(1, 'quarter').endOf('quarter')],
                '{{ trans('fi.first_quarter') }}': [moment().quarter(1).startOf('quarter'), moment().quarter(1).endOf('quarter')],
                '{{ trans('fi.second_quarter') }}': [moment().quarter(2).startOf('quarter'), moment().quarter(2).endOf('quarter')],
                '{{ trans('fi.third_quarter') }}': [moment().quarter(3).startOf('quarter'), moment().quarter(3).endOf('quarter')],
                '{{ trans('fi.fourth_quarter') }}': [moment().quarter(4).startOf('quarter'), moment().quarter(4).endOf('quarter')]
            }
        });

        $('#' + identifier + '_date_range').on('apply.daterangepicker', function (ev, picker) {

            $('#' + identifier + '_from_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#' + identifier + '_to_date').val(picker.endDate.format('YYYY-MM-DD'));
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD')).trigger('change');
        });

        $('#' + identifier + '_date_range').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            $('#' + identifier + '_from_date' + ',' + '#' + identifier + '_to_date' + ',' + '#' + identifier + '_date_range').val('');
            $('form#filter').submit();
        });

        if (startDate != '' && endDate != '') {
            endDate = moment(endDate).format('YYYY-MM-DD');
            startDate = moment(startDate).format('YYYY-MM-DD');
            $('#from_date').val(startDate);
            $('#to_date').val(endDate);
            $('#date_range').val(startDate + ' - ' + endDate);
        }
    }

    function initDateRangePreSelected(identifier) {
        var start = '{{ request('from_date','') }}';
        var end = '{{ request('to_date','') }}';

        if (start != '' && end != '') {
            start = moment(start).format('YYYY-MM-DD');
            end = moment(end).format('YYYY-MM-DD');
            $('#' + identifier + '_from_date').val(start);
            $('#' + identifier + '_to_date').val(end);
            $('#' + identifier + '_date_range').val(start + ' - ' + end);
        }
    }

</script>