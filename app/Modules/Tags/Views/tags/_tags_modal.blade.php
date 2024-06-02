<script type="text/javascript">
    $(function () {

        $('body #modal-{{$modalName}}-edit').modal();

        $('.btn-{{$modalName}}-cancel').click(function () {
            $('.delete-{{$modalName}}-active').removeClass('delete-{{$modalName}}-active');
        });
        $('#tags-category').click(function () {

            var $_this = $(this);

            if ($_this.val() != '') {
                $.post('{!! route('tags.category.wise.data') !!}', {
                    modalName: '{{$modalName}}',
                    category: $_this.val(),
                }).done(function (response) {
                    if (response.count > 0) {
                        $('#category-wise-records').removeClass('d-none').addClass('d-block');
                        if ($('#category-wise-select option').length > 0) {
                            $('#category-wise-select').html('');
                        }
                        var categoryWiseRecords = response.categoryWiseRecords;
                        $.each(categoryWiseRecords, function (key, val) {
                            if (key == '') {
                                $('#category-wise-select').append(new Option(val, key, true, true))
                            } else {
                                $('#category-wise-select').append(new Option(val, key));
                            }
                        });
                    } else {
                        $('#category-wise-records').addClass('d-none').removeClass('d-block');
                    }
                }).fail(function (response) {
                    showHideLoaderModal();
                    alertify.error($.parseJSON(response.responseText).message, 5);
                });

            }
        });

        $('#btn-{{$modalName}}-edit').click(function () {
            showHideLoaderModal();
            $.post('{!! route('tags.update.modal') !!}', {
                tag_id: $('#category-wise-select').val(),
                tag_name_update: $('#tags-name').val(),
            }).done(function (response) {
                showHideLoaderModal();
                $('#modal-{{$modalName}}-edit').modal('hide');
                alertify.success(response.message, 5);
            }).fail(function (response) {
                showHideLoaderModal();
                showAlertifyErrors($.parseJSON(response.responseText).errors);
            });

        });
    });
</script>

<div class="modal fade" id="modal-{{$modalName}}-edit" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg text-break">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> {{trans('fi.rename_tags')}} </h5>
                <button type="button" class="close btn-{{$modalName}}-cancel" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            {!! trans('fi.tag_category') !!}
                        </div>
                        <div class="col-md-8">
                            {!! Form::select('category', $category, null, ['class' => 'form-control form-control-sm tags-category','id' => 'tags-category', 'style' => 'width:100%']) !!}
                        </div>
                    </div>

                    <div id="category-wise-records" class="d-none">
                        <div class="dropdown-divider mt-4"></div>
                        <div class="row mt-4">
                            <div class="col-md-2">
                                {!! trans('fi.tag_name') !!}
                            </div>
                            <div class="col-md-3">
                                <select id="category-wise-select" name="category-wise-select"
                                        class="form-control form-control-sm"></select>
                            </div>
                            <div class="col-md-2">
                                {!! trans('fi.tag_new_name') !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::text('tag_new_name', null , ['class' => 'form-control form-control-sm tags-name','id' => 'tags-name']) !!}
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-primary"
                                        id="btn-{{$modalName}}-edit">{!! trans('fi.apply') !!}</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-1 pt-1">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-sm btn-outline-secondary float-right btn-{{$modalName}}-cancel"
                            data-dismiss="modal" id="btn-{{$modalName}}-cancel">
                        {{ trans('fi.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>