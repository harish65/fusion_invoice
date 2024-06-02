@include('layouts._select2')

<script type="text/javascript">
    $(function () {
        $('.custom-select2').select2();
    });
</script>

@foreach ($customFields as $customField)
    <div class="form-group">
        @if($customField->field_type != 'checkbox')
            <label class="col-sm-4 control-label">{{ $customField->field_label }}</label>
        @else
            <div class="col-sm-4">&nbsp;</div>
        @endif
        <div class="col-sm-8">
            @switch($customField->field_type)
                @case('checkbox')
                {!! Form::checkbox('custom[' . $customField->column_name . ']',1, isset($object->custom->{$customField->column_name}) && $object->custom->{$customField->column_name} == 1 ? true : false, ['class' => 'custom-form-field', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
                <label for="custom[{{$customField->column_name}}]">{{ $customField->field_label }}</label>
                @break
                @case('radio')
                @foreach($customField->options as $key => $option)
                    {!! Form::radio('custom[' . $customField->column_name . ']',$key,$key == $customField->default ? 'true':'',[isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} == $key ? 'checked' : '' :'' ,'class' => 'custom-form-field', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
                    <label for="custom[{{$customField->column_name.']'.$key}}" class="form-check-label">{{ $option }}</label>
                @endforeach
                @break
                @case('dropdown')
                {!! Form::select('custom[' . $customField->column_name . ']', $customField->options, (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : $customField->default), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
                @break
                @case('tagselection')
                {!! Form::select('custom[' . $customField->column_name . '][]', $customField->options, (isset($object->custom->{$customField->column_name}) ? json_decode($object->custom->{$customField->column_name}) : $customField->default), ['class' => 'custom-form-field form-control form-control-sm custom-select2','multiple' => 'multiple', 'data-role'=>'tagsinput', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
                @break
                @case('textarea')
                {!! Form::textarea('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'rows' => $customField->rows]) !!}
                @break
                @case('date')
                        <script type="text/javascript">
                            $(function () {
                                $('body').on('click', "#" + '{{$customField->column_name ."-only-payment-modal-date"}}', function () {

                                    $("#" + '{{$customField->column_name ."-only-payment-modal-date"}}').datetimepicker({
                                        format: dateFormat,
                                        autoclose: true,
                                        todayHighlight: true
                                    });
                                });

                            });
                        </script>
                    <div class="input-group date">
                        <div class="input-group date"
                             id={{$customField->column_name ."-only-payment-modal-date"}} data-target-input="nearest">
                            {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) && $object->custom->{$customField->column_name} != null ? \Carbon\Carbon::createFromFormat('Y-m-d', $object->custom->{$customField->column_name})->format(config('fi.dateFormat') ) : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-toggle' => 'datetimepicker', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'autocomplete' => 'off', 'data-label' => $customField->field_label ,'data-target' => '#'. $customField->column_name ."-only-payment-modal-date"]) !!}
                            <div class="input-group-append"
                                 data-target={{"#". $customField->column_name ."-only-payment-modal-date"}}  data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>
                    </div>
                @break

                @case('datetime')
                    <script type="text/javascript">
                        $(function () {
                            $('body').on('click', "#" + '{{ $customField->column_name ."-custom-field-modal-datetime-payment"}}', function () {

                                $("#" + '{{ $customField->column_name ."-custom-field-modal-datetime-payment"}}').datetimepicker({
                                    format: dateTimeFormat,
                                    icons: {time: 'far fa-clock'}
                                });

                            });
                        });
                    </script>

                <div class="form-group">
                    <div class="input-group date" id={{$customField->column_name ."-custom-field-modal-datetime-payment"}} data-target-input="nearest">
                        {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) && $object->custom->{$customField->column_name} != null ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $object->custom->{$customField->column_name})->format(config('fi.dateFormat') . (!config('fi.use24HourTimeFormat') ? ' g:i A' : ' H:i'))  : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-target' => '#'.$customField->column_name ."-custom-field-modal-datetime-payment",'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'autocomplete' => 'off', 'data-label' => $customField->field_label, 'data-toggle'=> 'datetimepicker']) !!}
                        <div class="input-group-append"
                             data-target={{"#".$customField->column_name ."-custom-field-modal-datetime-payment"}} data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>

                @break
                @case('currency')
                <div class="input-group">
                    @if(!empty($customField->symbol))
                        <div class="input-group-append">
                            <div class="input-group-text">{{ $customField->symbol }}</div>
                        </div>
                    @endif
                    {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
                </div>
                @break
                @case('image')
                @if(isset($object->custom->{$customField->column_name}))
                    <div class="custom_img">{!! $object->custom->image($customField->column_name,100) !!}</div>
                @endif
                {!! Form::file('custom[' . $customField->column_name . ']', ['class' => 'custom-form-field', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
                @break
                @case('phone')
                @case('decimal')
                @case('integer')
                @case('url')
                @case('email')
                {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
                @break
                @default
                {!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]]) !!}
                @endswitch
        </div>
    </div>
@endforeach