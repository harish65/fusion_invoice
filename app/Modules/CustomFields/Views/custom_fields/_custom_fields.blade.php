<div class="form-group">
    @if($customField->field_type != 'checkbox')
        <label>{{ $customField->field_label }}:</label>
    @endif
    @switch($customField->field_type)
        @case('checkbox')
        {!! Form::checkbox('custom[' . $customField->column_name . ']',1, isset($object->custom->{$customField->column_name}) && $object->custom->{$customField->column_name} == 1 ? true : false, ['class' => 'custom-form-field', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'data-type'=> 'checkbox' ,'data-label' => $customField->field_label ]) !!}
        <label for="custom[{{$customField->column_name}}]">{{ $customField->field_label }}:</label>
        @break
        @case('radio')
        @foreach($customField->options as $radio_key => $option)
            <div class="form-check">

                {!! Form::radio('custom[' . $customField->column_name . ']'.$key,$radio_key,$radio_key == $customField->default ? 'true':'',[isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} == $radio_key ? 'checked' : '' :'' ,'id' =>'custom['.$customField->column_name.']'.$radio_key.$key ,'class' => 'form-check-input', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'data-label' => $customField->field_label ,'data-type'=> 'radio' ,'data-value'=> $radio_key]) !!}
                <label for="custom[{{$customField->column_name.']'.$radio_key.$key}}"
                       class="form-check-label">{{ $option }}</label>

            </div>
        @endforeach
        @break
        @case('dropdown')
        {!! Form::select('custom[' . $customField->column_name . ']', $customField->options, (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : $customField->default), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'data-label' => $customField->field_label ,'data-type'=> 'dropdown' , 'autocomplete' => 'off']) !!}
        @break
        @case('tagselection')
        {!! Form::select('custom[' . $customField->column_name . '][]', $customField->options, (isset($object->custom->{$customField->column_name}) ? json_decode($object->custom->{$customField->column_name}) : $customField->default), ['class' => 'custom-form-field form-control form-control-sm custom-select2','multiple' => 'multiple', 'data-role'=>'tagsinput', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name,'data-type'=> 'tagselection' , 'data-label' => $customField->field_label , 'autocomplete' => 'off']) !!}
        @break
        @case('textarea')
        {!! Form::textarea('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'rows' => $customField->rows, 'data-label' => $customField->field_label ,'data-type'=> 'textarea' , 'autocomplete' => 'off']) !!}
        @break
        @case('date')
        <script type="text/javascript">
            $(function () {

                $('body').on('click', "#" + '{{$customField->column_name ."-only-date-".$key}}', function () {

                    $("#" + '{{$customField->column_name ."-only-date-".$key}}').datetimepicker({
                        autoclose: true,
                        format: dateFormat
                    });

                });

            });
        </script>
        <div class="input-group date">
            <div class="input-group date"
                 id={{$customField->column_name ."-only-date-".$key}} data-target-input="nearest">

                {!! Form::text('custom[' . $customField->column_name . ']',
                 (isset($object->custom->{$customField->column_name}) && $object->custom->{$customField->column_name} != null ? \Carbon\Carbon::createFromFormat('Y-m-d', $object->custom->{$customField->column_name})->format(config('fi.dateFormat') ) : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-toggle' => 'datetimepicker', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'autocomplete' => 'off', 'data-label' => $customField->field_label ,'data-target' => '#'. $customField->column_name .'-only-date-'.$key , 'data-type'=> 'date' ,'autocomplete' => 'off']) !!}
                <div class="input-group-append"
                     data-target={{"#". $customField->column_name ."-only-date-".$key}}  data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>

            </div>
        </div>
        @break

        @case('datetime')

        <script type="text/javascript">
            $(function () {

                $('body').on('click', "#" + '{{$customField->column_name ."-custom-field-datetime-".$key}}', function () {

                    $("#" + '{{$customField->column_name ."-custom-field-datetime-".$key}}').datetimepicker({
                        format: dateTimeFormat,
                        icons: {time: 'far fa-clock'}

                    });

                });
            });
        </script>

        <div class="form-group">
            <div class="input-group date" id="{{$customField->column_name ."-custom-field-datetime-".$key}}"
                 data-target-input="nearest">
                {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) && $object->custom->{$customField->column_name} != null ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $object->custom->{$customField->column_name})->format(config('fi.dateFormat') . (!config('fi.use24HourTimeFormat') ? ' g:i A' : ' H:i'))  : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-target' => "#".$customField->column_name ."-custom-field-datetime-".$key,'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'autocomplete' => 'off', 'data-label' => $customField->field_label, 'data-toggle'=> 'datetimepicker' , 'data-type'=> 'datetime' , 'autocomplete' => 'off']) !!}
                <div class="input-group-append"
                     data-target={{"#".$customField->column_name ."-custom-field-datetime-".$key}} data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>

        @break

        @case('currency')
        <div class="input-group input-group-sm mb-3">
            @if(!empty($customField->symbol))
                <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">{{ $customField->symbol }}</span>
                </div>
            @endif
            {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'data-label' => $customField->field_label ,'aria-describedby'=>'inputGroup-sizing-sm' ,'data-type'=> 'currency' , 'autocomplete' => 'off']) !!}
        </div>
        @break
        @case('image')

        @if(isset($object->custom->{$customField->column_name}))
            <div class="custom_img">{!! $object->custom->image($customField->column_name,100) !!}</div>
        @endif
        <div class="custom-file">
            {!! Form::file('custom[' . $customField->column_name . ']', ['class' => 'custom-file-input', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'data-label' => $customField->field_label ,'data-type'=> 'image' , 'autocomplete' => 'off']) !!}
            <label class="custom-file-label" for="customFile">{{ trans('fi.choose-file') }}</label>
        </div>

        <script>
            $(".custom-file-input").on("change", function () {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });
        </script>
        @break
        @case('url')
        {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), [ 'autocomplete' => 'off' , 'class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'placeholder' => 'http://','data-type'=> 'url' , 'data-label' => $customField->field_label]) !!}
        @break
        @case('phone')
        @case('decimal')
        @case('integer')
        {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['autocomplete' => 'off' , 'class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name,'data-type'=> 'integer' , 'data-label' => $customField->field_label]) !!}
        @break
        @case('email')
        {!! Form::text('custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['autocomplete' => 'off' , 'class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name,'data-type'=> 'email' , 'data-label' => $customField->field_label]) !!}
        @break
        @default
        {!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control form-control-sm', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name, 'data-label' => $customField->field_label]]) !!}
    @endswitch
</div>
