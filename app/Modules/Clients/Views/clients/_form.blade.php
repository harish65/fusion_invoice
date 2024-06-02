@include('layouts._select2')
@include('clients._js_lookup')
<div class="row">
    <div class="col-md-3 client-active-resize">
        <div class="form-group">
            <label>* {{ trans('fi.client_name') }}:</label>
            {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3 client-active-resize">
        <div class="form-group">
            <label>{{ trans('fi.email_address') }}: </label>
            {!! Form::text('client_email', null, ['id' => 'client_email', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3 client-active-resize">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{!! trans('fi.tt_client_type') !!}">{{ trans('fi.type') }}:</label>
            {!! Form::select('type', $types, $editMode == true ? null : config('fi.defaultClientType') , ['id' => 'type', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    @if(config('fi.clientColumnSettingsVatTaxId') == 1)
        <div class="col-md-3 client-active-resize">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto"
                       title="{!! trans('fi.tt_vat_tax_id') !!}">{{ trans('fi.vat_tax_id') }}:</label>
                {!! Form::text('vat_tax_id', null, ['id' => 'vat_tax_id', 'class' => 'form-control form-control-sm']) !!}
            </div>
        </div>
    @endif
</div>

<div class="container-fluid fi-form-area alt-fi-form-area-color">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ trans('fi.address') }}: </label>
                {!! Form::textarea('address', null, ['id' => 'address', 'class' => 'form-control form-control-sm', 'rows' => 4]) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="auto"
                       title="{!! trans('fi.tt_client_tags') !!}">{{ trans('fi.tags') }}: </label>
                {!! Form::select('tags[]', $tags, $selectedTags, ['class' => 'form-control form-control-sm client-tags','multiple' => true, 'id' => 'client-tags', 'style' => 'width:100%']) !!}
            </div>
        </div>
    </div>
    <div class="row">
        @if(config('fi.clientColumnSettingsCity') == 1)
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ trans('fi.city') }}: </label>
                    {!! Form::text('city', null, ['id' => 'city', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif
        @if(config('fi.clientColumnSettingsState') == 1)
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ trans('fi.state') }}: </label>
                    {!! Form::text('state', null, ['id' => 'state', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif
        @if(config('fi.clientColumnSettingsPostalCode') == 1)

            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ trans('fi.postal_code') }}: </label>
                    {!! Form::text('zip', null, ['id' => 'zip', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif

        @if(config('fi.clientColumnSettingsCountry') == 1)
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ trans('fi.country') }}: </label>
                    {!! Form::select('country', $countries, null, ['id' => 'country', 'class' => 'form-control form-control-sm', 'placeholder' => '']) !!}
                </div>
            </div>
        @endif

    </div>

    <div class="row">
        @if(config('fi.clientColumnSettingsPhoneNumber') == 1)
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ trans('fi.phone_number') }}: </label>
                    {!! Form::text('phone', null, ['id' => 'phone', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif

        @if(config('fi.clientColumnSettingsFaxNumber') == 1)
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ trans('fi.fax_number') }}: </label>
                    {!! Form::text('fax', null, ['id' => 'fax', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif

        @if(config('fi.clientColumnSettingsMobileNumber') == 1)
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ trans('fi.mobile_number') }}: </label>
                    {!! Form::text('mobile', null, ['id' => 'mobile', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif
    </div>
</div>

<div class="container-fluid fi-form-area">
    <div class="row">
        @if(config('fi.clientColumnSettingsWebAddress') == 1)
            <div class="col-md-5">
                <div class="form-group">
                    <label>{{ trans('fi.web_address') }}: </label>
                    {!! Form::text('web', null, ['id' => 'web', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif

        @if(config('fi.clientColumnSettingsSocialMediaUrl') == 1)
            <div class="col-md-5">
                <div class="form-group">
                    <label>{{ trans('fi.social_media_url') }}: </label>
                    {!! Form::text('social_media_url', null, ['id' => 'social_media_url', 'class' => 'form-control form-control-sm']) !!}
                </div>
            </div>
        @endif
    </div>
</div>

<div class="container-fluid fi-form-area alt-fi-form-area-color">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <span style="color: firebrick;background-color: pink;">
                <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_important_note') !!}">Important
                    Note: </label>
                </span>
                {!! Form::textarea('important_note', null, ['id' => 'important_note', 'class' => 'form-control form-control-sm', 'rows' => 3]) !!}
            </div>
        </div>
        @if(config('fi.clientColumnSettingsGeneralNotes') == 1)
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ trans('fi.general_notes') }}: </label>
                    {!! Form::textarea('general_notes', null, ['id' => 'general_notes', 'class' => 'form-control form-control-sm', 'rows' => 3]) !!}
                </div>
            </div>
        @endif
    </div>
</div>

<div class="container-fluid fi-form-area">
    <div class="row">
        @if(config('fi.clientColumnSettingsLeadSource') == 1)
            <div class="col-md-3">
                <div class="form-group">
                    <label data-toggle="tooltip" data-placement="auto"
                           title="{!! trans('fi.tt_client_lead_source_tags') !!}">{{ trans('fi.lead_source') }}
                        : </label>
                    {!! Form::select('lead_source_tag_id', $leadSourceTags, $selectedLeadSourceTags, ['class' => 'form-control form-control-sm client-lead-source-tag','multiple' => false, 'id' => 'client-lead-source-tag', 'style' => 'width:100%']) !!}
                </div>
            </div>
        @endif
        @if(config('fi.clientColumnSettingsLeadSourceNotes') == 1)
            <div class="col-md-9">
                <div class="form-group">
                    <label>{{ trans('fi.lead_source_notes') }}: </label>
                    {!! Form::textarea('lead_source_notes', null, ['id' => 'lead_source_notes', 'class' => 'form-control form-control-sm', 'rows' => 2]) !!}
                </div>
            </div>
        @endif
    </div>
</div>

<div class="container-fluid fi-form-area alt-fi-form-area-color">
    <div id="custom-body-table">
        @if ($customFields)
            @include('custom_fields._custom_fields_unbound', ['object' => isset($client) ? $client : []])
        @endif
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#name').focus();

        $('#client-lead-source-tag').select2({tags: true, allowClear: true, selectOnClose: true});

        $('#client-tags').select2({tags: true, tokenSeparators: [",", " "]});

        $('#btn-delete-custom-img').click(function () {
            var url = "{{ route('clients.deleteImage', [isset($client->id) ? $client->id : '','field_name' => '']) }}";
            $.post(url + '/' + $(this).data('field-name')).done(function () {
                $('.custom_img').html('');
            });
        });

        $('#country').select2({
            placeholder: "{{ trans('fi.select_country') }}"
        });

        $(document).on("click", ".select2-selection__clear", function () {
            $('#client-lead-source-tag').select2({
                tags: true,
                allowClear: true,
                selectOnClose: true
            }).val('').trigger('change');
        });
    });
</script>
