<script type="text/javascript">
    $(function () {
        $(document).ready(function () {
            $('#select-all-columns').prop('checked', false);
            var isAllChecked = eachColumnSelectedOrNot();
            if (isAllChecked == 1) {
                $('#select-all-columns').prop('checked', true);
            } else {
                $('#select-all-columns').prop('checked', false);
            }
        });

        $('body #modal-columns-setting').modal();

        $('#btn-submit-column-setting').click(function (evt) {
            var $_this = $(this);
            evt.stopPropagation();
            evt.preventDefault();
            var formData = $('#columns-filter').serializeFormJSON();

            let $selectedColumns = $('.filter-column-chk:checked');
            let obj = {};
            $selectedColumns.each(function () {
                var $_this = $(this);
                var $_key = $_this.data('column-name');
                obj[$_key] = $_this.val() + '###' + $_this.data('column-sortable');
            });
            $.each(obj, function (objIndex, objValue) {
                $.each(formData, function (index, value) {
                    var filter_column = index.substring(0, index.lastIndexOf("]") + 1);
                    var result = filter_column.startsWith("columns", 0);
                    if (result == true) {
                        if ('columns[' + objIndex + ']' == filter_column) {
                            formData[index] = objValue;
                        }
                    }
                });
            });

            $.post($_this.data('url'), formData)
                .done(function () {
                    $('#modal-columns-setting').modal('hide');
                    window.location.replace(document.URL);
                }).fail(function (response) {
                $.each($.parseJSON(response.responseText).errors, function (id, message) {
                    alertify.error(message[0], 5);
                });
            });

        });

        $('#select-all-columns').click(function () {
            if ($(this).prop('checked')) {
                $('.filter-column-chk').prop('checked', true);

            } else {
                $('.filter-column-chk').prop('checked', false);
            }
        });

        $('.filter-column-chk').click(function () {

            $('#select-all-columns').prop('checked', false);

            if ($(this).prop('checked')) {
                var isAllChecked = eachColumnSelectedOrNot();
                if (isAllChecked == 1) {
                    $('#select-all-columns').prop('checked', true);
                }
            } else {
                $('#select-all-columns').prop('checked', false);
            }
        });

        function eachColumnSelectedOrNot() {
            var isAllChecked = 1;

            $('.filter-column-chk').each(function () {
                if (!this.checked)
                    isAllChecked = 0;
            });
            return isAllChecked;
        }
        $('.btn-recurring-clear-column-filter').click(function (){
           $('.btn-action-modal').removeClass('disabled');
        });
    });
</script>

<div class="modal fade" id="modal-columns-setting" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog text-break">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{trans('fi.select_columns')}} </h5>
                <button type="button" class="close btn-recurring-clear-column-filter" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form id="columns-filter">

                <div class="modal-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                @if($recurringInvoiceColumnSettings != '' && $recurringInvoiceColumnSettings != null)
                                    @foreach($defaultRecurringInvoiceSequenceColumnsData as $defaultKey => $defaultValue)
                                        @foreach($recurringInvoiceColumnSettings as $key => $value)
                                            @if($defaultKey == $key)
                                                <div class="col-md-6 column-list-item float-left">
                                                    <div class="form-group filter-column-item">
                                                        <label>
                                                            {!! Form::checkbox('columns['.$key.']', null,($value[0] == 'on' ) ? true : false, ['class'=>'filter-column-chk' ,'data-column-name' => $key ,'data-column-sortable'=> $value[1]]) !!} {{trans('fi.'.$key)}}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="col-md-6 column-list-item float-left">
                                    <div class="form-group filter-column-item">
                                        <label>
                                            {!! Form::checkbox('select_all_columns', null,null, ['class' => 'all-selected' ,'id' => 'select-all-columns']) !!} {{trans('fi.all_selected')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="modal-footer">

                    <div class="col-sm-12">

                        <button type="button" id="btn-submit-column-setting" data-url="{{ route('recurring.invoice.store.filterColumnSetting') }}" class="btn btn-sm btn-primary float-right ml-2">{{ trans('fi.submit') }}</button>
                        <button type="button" class="btn btn-sm btn-default float-right btn-recurring-clear-column-filter" data-dismiss="modal" id="btn-clear-column-filter">{{ trans('fi.close') }}</button>

                    </div>

                </div>

            </form>

        </div>
    </div>
</div>
