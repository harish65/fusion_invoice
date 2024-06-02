<script type="text/javascript">
    $(function () {
        $('.custom-select2').select2();
    });
</script>

@if(isset($label))
    <div class="row custom-fields">
        @foreach ($customFields as $key => $customField)
            @if($customField->field_label == $label)
                <div class="col-md-{{ config('fi.customFieldsDisplayColumn') }}">
                    @include('custom_fields._custom_fields' ,['key' => $key.$key])
                </div>
            @endif
        @endforeach
    </div>
@else
    <div class="row custom-fields">
        @foreach ($customFields as $key => $customField)
            <div class="col-md-{{ config('fi.customFieldsDisplayColumn') }}">
                @include('custom_fields._custom_fields' ,['key' => $key.$key])
            </div>
        @endforeach
    </div>
@endif
