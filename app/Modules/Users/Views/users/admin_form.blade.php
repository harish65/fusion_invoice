@extends('layouts.master')

@section('javascript')

    @include('layouts._colorpicker')
    @include('layouts._select2')

@stop

@section('content')
    <script type="text/javascript">
        $(function () {

            $("form").one('submit', function myFormSubmitCallback(evt) {
                evt.stopPropagation();
                evt.preventDefault();
                var formAllData = $(this).serializeFormJSON();
                var customFields = {};
                var missingValues = {};
                var selectCustomRadioButtonValue = null;
                $('#custom-body-table').find('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                    var fieldName = $(this).data('users-field-name');
                    var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                    if (fieldName !== undefined) {
                        if ('file' === inputType) {
                            customFields[fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];
                            return true;
                        }
                        if ('select' === inputType) {
                            if ($(this).find('option:selected').length == 0) {
                                customFields[fieldName] = '';
                                return true;
                            }
                        }
                        if ('checkbox' === inputType) {
                            customFields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                            return true;
                        }
                        if ('radio' === inputType) {
                            if ($(this).prop('checked') == true) {
                                customFields[fieldName] = $(this).val();
                                selectCustomRadioButtonValue = $(this).val();
                            }
                            if ($(this).prop('checked') == false && selectCustomRadioButtonValue == null) {
                                customFields[fieldName] = 'null';
                            }
                            return customFields[fieldName];
                        }
                        customFields[$(this).data('users-field-name')] = $(this).val();
                    }
                });
                $.each(customFields, function (customKey, valueCustom) {
                    $.each(formAllData, function (formKey, formValue) {
                        var filter_column = formKey.substring(0, formKey.lastIndexOf("]") + 1);
                        var result = filter_column.startsWith("custom", 0);
                        if (result == true) {
                            if ('custom[' + customKey + ']' != filter_column) {
                                return missingValues['custom[' + customKey + ']'] = valueCustom;
                            }
                        } else {
                            return missingValues['custom[' + customKey + ']'] = valueCustom;
                        }
                    });
                });
                $.each(missingValues, function (key, value) {
                    var input = $("<input>").attr("type", "hidden").attr("class", "missingValue").attr("name", key).val(value);
                    $("form").append($(input));
                });
                $(this).submit();
            });

            $('#name').focus();

            $('#btn-delete-custom-img').click(function () {
                var url = "{{ route('users.deleteImage', [isset($user->id) ? $user->id : '','field_name' => '']) }}";
                $.post(url + '/' + $(this).data('field-name')).done(function () {
                    $('.custom_img').html('');
                });
            });

            $(document).ready(function () {
                $('.permission-is_view').not(':checked').each(function () {
                    $(this).closest('tr')
                        .find('.permission-checkbox').not('.permission-is_view')
                        .prop('checked', false)
                        .prop('disabled', true);
                });
            });

            $('.addons,.reports,.dashboards').change(function () {
                if (!this.checked) {
                    $("#all-permissions").prop('checked', false);
                }
            });

            $('.permission-is_view,.permission-is_create,.permission-is_update,.permission-is_delete').change(function () {
                if (!this.checked) {
                    $(this).closest('tr').find('.check-all').prop('checked', false);
                    $("#all-permissions").prop('checked', false);
                } else {
                    var chk_count = 0;
                    $(this).closest('tr')
                        .find('.permission-checkbox').not(':checked').each(function () {
                        chk_count++;
                    });
                    if (chk_count == 0) {
                        $(this).closest('tr').find('.check-all').prop('checked', true);
                    }
                }
            });

            $('.permission-is_view').change(function () {
                if (this.checked) {
                    $(this).closest('tr')
                        .find('.permission-checkbox').not('.permission-is_view')
                        .prop('checked', false)
                        .removeAttr('disabled');
                } else {
                    $(this).closest('tr')
                        .find('.permission-checkbox').not('.permission-is_view')
                        .prop('checked', false)
                        .prop('disabled', true);
                    $("#all-permissions").prop('checked', false);
                }
            });

            $('.check-all').change(function () {
                if (this.checked) {
                    $(this).closest('tr')
                        .find('.permission-checkbox')
                        .prop('checked', true)
                        .removeAttr('disabled');
                } else {
                    $(this).closest('tr')
                        .find('.permission-checkbox')
                        .prop('checked', false)
                        .prop('disabled', true);
                    $("#all-permissions").prop('checked', false);
                }
            });

            $("#all-permissions").click(function () {
                if (this.checked) {
                    $('#tab-general').find('input[type=checkbox]').prop('checked', true).removeAttr('disabled');
                } else {
                    $('#tab-general').find('input[type=checkbox]').prop('checked', false);
                    $('.permission-is_create,.permission-is_update,.permission-is_delete').prop('disabled', true);
                }
            });

        });

        $(document).ready(function () {
            if ('standard_user' === '{{ $userType }}') {
                $('.permissions-box').removeClass('d-none').show();
            }
        });

        $('body')
            .on('change', '.user-type-select', function () {
                if ('standard_user' === $(this).children('option:selected').val()) {
                    $('.permissions-box').removeClass('d-none').show();
                } else {
                    $('.permissions-box').addClass('d-none');
                }
            })
            .on('click', '#copy_permission', function (e) {
                e.preventDefault();
                let userId = $(this).parent().prev().children('.copy-permission-box').find('#permissions_copied_from').children('option:selected').val();

                if (userId != '') {
                    $.get('{{ url('/') }}' + '/users/' + userId + '/permissions', function (data) {
                        $(".permission-checkbox").attr("checked", false);
                        for (let i = 0; i < data.length; i++) {
                            let item = data[i];
                            if (item.is_view) {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_view]"]')
                                    .prop('checked', true)
                                    .closest('tr').find('.permission-checkbox').removeAttr('disabled');
                            } else {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_view]"]')
                                    .prop('checked', false)
                                    .closest('tr').find('.permission-checkbox').prop('checked', false).prop('disabled', true);
                            }
                            if (item.is_create) {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_create]"]').prop('checked', true);
                            } else {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_create]"]').prop('checked', false);
                            }
                            if (item.is_update) {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_update]"]').prop('checked', true);
                            } else {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_update]"]').prop('checked', false);
                            }
                            if (item.is_delete) {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_delete]"]').prop('checked', true);
                            } else {
                                $('.permission-checkbox[name="permissions[' + item.module + '][is_delete]"]').prop('checked', false);
                            }
                        }

                    });
                } else {
                    alertify.error('{{ trans('fi.please_select_user') }}', 5);
                }
            });
    </script>
    @include('users._js_initials_colorpicker')

    @if ($editMode == true)
        {!! Form::model($user, ['route' => ['users.update', $user->id] , 'id' => 'userModelEditMode','enctype' => 'multipart/form-data']) !!}

        {!!  Form::hidden('user_id',$user->id,['id' => 'user_id']) !!}
        {!!  Form::hidden('custom_module', 'user',['id' => 'custom_module']) !!}
    @else
        {!! Form::open(['route' => ['users.store'] ,'id' => 'userModelEditMode','enctype' => 'multipart/form-data']) !!}
    @endif

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_users_about') !!}">
                        {{ trans('fi.user') }}</h1>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        @if ($editMode)
                            <a href="{{ $returnUrl }}" class="btn btn-sm btn-default"><i
                                        class="fa fa-backward"></i> {{ trans('fi.back') }}</a>
                        @endif
                        <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('layouts._alerts')
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline card-outline-tabs">

                        <div class="card-body">
                            <div class="tab-content" id="tab-general">
                                <!-- General tab start -->
                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="card card-primary card-outline">

                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ trans('fi.name') }}: </label>
                                                            {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ trans('fi.email') }}: </label>
                                                            {!! Form::text('email', null, ['id' => 'email', 'class' => 'form-control form-control-sm']) !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ trans('fi.initials') }}: </label>
                                                            {!! Form::text('initials', null, ['id' => 'initials', 'class' => 'form-control form-control-sm', 'maxlength' => 2]) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ trans(('fi.initials_bg_color')) }}: </label>

                                                            <div class="input-group colorpicker-element">
                                                                {!! Form::text('initials_bg_color', null, ['class' => 'form-control form-control-sm fi-colorpicker initials-bg-color', 'readonly' => true]) !!}
                                                                <div class="input-group-append">
                                                                        <span class="input-group-text"><i
                                                                                    class="fas fa-square"
                                                                                    style="{{ isset($user->initials_bg_color) && $user->initials_bg_color != '' ? 'color:'.$user->initials_bg_color : '' }}"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if (!$editMode)
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('fi.password') }}: </label>
                                                                {!! Form::password('password', ['id' => 'password', 'class' => 'form-control form-control-sm']) !!}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ trans('fi.password_confirmation') }}
                                                                    : </label>
                                                                {!! Form::password('password_confirmation', ['id' => 'password_confirmation',
                                                            'class' => 'form-control form-control-sm']) !!}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ trans('fi.user_role') }}: </label>
                                                            {!! Form::select('user_type', $userTypes, $userType, ['id' => 'user_type', 'class' => 'form-control form-control-sm user-type-select']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>{{ trans('fi.status') }}: </label>
                                                            {!! Form::select('status', $status, null, ['id' => 'status', 'class' => 'form-control form-control-sm', isset($user->id) && auth()->user()->id == $user->id ? 'disabled' : '']) !!}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="card card-primary card-outline permissions-box d-none">

                                            <div class="card-header">
                                                <h3 class="card-title">{{ trans('fi.permissions') }}</h3>

                                                <div class="card-tools">

                                                    <ul class="nav nav-pills ml-auto">

                                                        <li class="nav-item mt-1 mb-1 mr-1">
                                                            <label>{{ trans('fi.copy_from') }}: </label>
                                                        </li>
                                                        <li class="nav-item mt-1 mb-1 mr-1">
                                                            <div class="copy-permission-box">
                                                                {!! Form::select('permissions_copied_from', $permissionsCopiedFrom, null, ['id' => 'permissions_copied_from', 'class' => 'form-control form-control-sm copy-permission-select']) !!}
                                                            </div>
                                                        </li>
                                                        <li class="nav-item mt-1 mb-1 mr-1">
                                                            <button class="btn btn-sm btn-primary btn-copy-permissions"
                                                                    id="copy_permission"><i
                                                                        class="fa fa-copy"></i> {{ trans('fi.copy') }}
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>

                                            <div class="card-body">

                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input id="all-permissions" type="checkbox">
                                                            <label for="all-permissions">{{ trans('fi.select_all_permissions') }} </label>
                                                        </div>
                                                        <table class="table table-hover table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th class="vertical-header-20">{{ trans('fi.modules') }}</th>
                                                                @foreach($permissibleItems['modules'][0]['actions'] as $action)
                                                                    <th class="hidden-sm hidden-xs">{{ trans('fi.' . $action) }}</th>
                                                                @endforeach
                                                                <th>{{ trans('fi.check-all') }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($permissibleItems['modules'] as $module)
                                                                <tr>
                                                                    <td class="vertical-header-20">{{ $module['name'] }}</td>
                                                                    @foreach($module['actions'] as $action)
                                                                        <td>{!! Form::checkbox('permissions[' . $module['slug'] . '][' . $action . ']', true, (1 == ($permissions[$module['slug']][$action] ?? 0)), ['class' => 'permission-checkbox permission-' . $action]) !!}</td>
                                                                    @endforeach
                                                                    <td class="vertical-header-20">
                                                                        {!! Form::checkbox('check-all', true, null, ['class' => 'check-all']) !!}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        <hr>
                                                        <table class="table table-hover table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th class="vertical-header-20">{{ trans('fi.reports') }}</th>
                                                                @foreach($permissibleItems['reports'][0]['actions'] as $action)
                                                                    <th class="hidden-sm hidden-xs">{{ trans('fi.' . $action) }}</th>
                                                                @endforeach
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($permissibleItems['reports'] as $report)
                                                                <tr>
                                                                    <td width="83%">{{ $report['name'] }}</td>
                                                                    @foreach($report['actions'] as $action)
                                                                        <td>{!! Form::checkbox('permissions[' . $report['slug'] . '][' . $action . ']', true, (1 == ($permissions[$report['slug']][$action] ?? 0)), ['class' => 'reports']) !!}</td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        <hr>
                                                        <table class="table table-hover table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th class="vertical-header-20">{{ trans('fi.dashboards') }}</th>
                                                                @foreach($permissibleItems['dashboards'][0]['actions'] as $action)
                                                                    <th class="hidden-sm hidden-xs">{{ trans('fi.' . $action) }}</th>
                                                                @endforeach
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($permissibleItems['dashboards'] as $dashboard)
                                                                <tr>
                                                                    <td width="83%">{{ $dashboard['name'] }}</td>
                                                                    @foreach($dashboard['actions'] as $action)
                                                                        <td>{!! Form::checkbox('permissions[' . $dashboard['slug'] . '][' . $action . ']', true, (1 == ($permissions[$dashboard['slug']][$action] ?? 0)), ['class' => 'dashboards']) !!}</td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        @if(isset($enabledAddons) && $enabledAddons->count() > 0)
                                                            <hr>
                                                            <table class="table table-hover table-striped">
                                                                <thead>
                                                                <tr>
                                                                    <th class="vertical-header-20">{{ trans('fi.addons') }}</th>
                                                                    @if(isset($permissibleItems['addons'][$enabledAddons[0]->name]['actions']))
                                                                        @foreach($permissibleItems['addons'][$enabledAddons[0]->name]['actions'] as $action)
                                                                            <th class="hidden-sm hidden-xs">{{ trans('fi.' . $action) }}</th>
                                                                        @endforeach
                                                                    @endif
                                                                    <th>{{ trans('fi.check-all') }}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($enabledAddons as $addon)
                                                                    @if(isset($permissibleItems['addons'][$addon->name]) && ($permissibleItems['addons'][$addon->name]['name'] == $addon->name))
                                                                        <tr>
                                                                            <td class="vertical-header-20">{{ $permissibleItems['addons'][$addon->name]['name'] }}</td>
                                                                            @foreach($permissibleItems['addons'][$addon->name]['actions'] as $action)
                                                                                <td>{!! Form::checkbox('permissions[' . $permissibleItems['addons'][$addon->name]['slug'] . '][' . $action . ']', true, (1 == ($permissions[$permissibleItems['addons'][$addon->name]['slug']][$action] ?? 0)), ['class' => 'addons permission-checkbox permission-' . $action]) !!}</td>
                                                                            @endforeach
                                                                            <td width="17%">
                                                                                {!! Form::checkbox('check-all', true, null, ['class' => 'check-all']) !!}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        @if ($customFields)
                                            <div class="card card-primary card-outline">

                                                <div class="card-header">
                                                    <h3 class="card-title">{{ trans('fi.custom_fields') }}</h3>
                                                </div>

                                                <div class="card-body" id="custom-body-table">

                                                    @include('custom_fields._custom_fields_unbound', ['object' => isset($user) ? $user : []])

                                                </div>

                                            </div>
                                        @endif

                                    </div>

                                </div>
                                <!-- General tab end -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {!! Form::close() !!}
@stop