{!! Form::open(['route' => ['export.export', $exportType], 'class' => 'form-horizontal export-form', 'id' => 'export-form', 'target' => '_blank']) !!}

<div class="col-md-12">
    <div class="form-group row">
        {!! Form::hidden('type', $exportType , ['id' => 'mapping-type']) !!}
        <label class="control-label p-lg-1" for="mapping-dropdown">{{ trans('fi.named_exports') }}:</label>

        <div class="col-4">
            <select class="form-control form-control-sm" id="mapping-dropdown" name="mapping">
                <option value="">{{trans('fi.select_named_export')}}</option>
                @foreach($mappingOptions as $mapping)
                    <option data-is-default="{{($mapping->is_default) ? '1' : '0'}}"
                            value="{{$mapping->id}}" {{($defaultMapping && $mapping->id == $defaultMapping->id) ? 'selected' : ''}}>{{ $mapping->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<hr style="margin: 2px 0 10px 0;">
<div class="row">
    <div class="col-md-12">
        <div class="pull-right form-group">
            <label class="pull-left control-label" for="format">{{ trans('fi.format') }}:</label>

            <div class="col-sm-6">
                {!! Form::select('format', $format, ($defaultMapping) ? $defaultMapping->format : null, ['class' => 'form-control form-control-sm', 'id' => 'format','style' => 'width: 300px;']) !!}
            </div>
        </div>
    </div>
</div>
<div class="clearfix row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="pull-left control-label" style="padding:  0 15px;">{{ trans('fi.fields_to_export') }}:</label>

            <div class="clearfix mb-10"></div>
            @foreach($fields as $field)
                <div class="col-md-6">
                    @if($defaultMapping && is_array($defaultMapping->description))
                        <input type="checkbox" id="{{ 'lbl_' . $field }}" name="fields[]" value="{{ $field }}"
                               class="adjust-checkbox" {{(in_array($field,$defaultMapping->description)) ? 'checked' : ''}}>
                    @else
                        <input type="checkbox" id="{{ 'lbl_' . $field }}" name="fields[]" value="{{ $field }}"
                               class="adjust-checkbox" checked>
                    @endif
                    <label for="{{ 'lbl_' . $field }}"> {{ ucwords(str_replace("_", " ", $field)) }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="row clearfix mt-10">
    <div class="col-md-12">
        <button type="button" id="save-mapping" class="btn btn-sm btn-success">
            <i class="fa fa-save"></i>&nbsp;{{ trans('fi.save_named_export') }}
        </button>
        <button type="button" id="delete-mapping" class="btn btn-sm btn-danger"
                style="display: {{($defaultMapping) ? '' : 'none'}}">
            <i class="fa fa-trash"></i>&nbsp;{{ trans('fi.delete_named_export') }}
        </button>
        <button class="btn btn-sm btn-primary pull-right"><i class="fa fa-download"></i> {{ $exportLbl  }}</button>
    </div>
</div>
{!! Form::close() !!}
<div class="modal fade" id="modal-mappings" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.save_named_export') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="save-mapping-form">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.name') }}</label>

                        <div class="col-sm-7">
                            {!! Form::text('name', '' , ['class' => 'form-control form-control-sm', 'id' => 'mapping-name']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"></label>

                        <div class="col-sm-7">
                            {!! Form::checkbox('is_default', 1, 0, ['id' => 'is-default-mapping', 'class'=>'check check-aligned']) !!}
                            <label class="form-check-label" style="margin-left: 10px;"
                                   for="is-default-mapping">{{ trans('fi.save_as_default_export') }}</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                @can('exports.view')
                <button type="button" id="submit-mappings" class="btn btn-sm btn-primary"
                        data-text="{{ trans('fi.submit') }}"
                        data-loading-text="{{ trans('fi.please_wait') }}...">{{ trans('fi.submit') }}</button>
                @endcan
            </div>
        </div>
    </div>
</div>