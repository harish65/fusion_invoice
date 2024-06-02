<?php

$translations = [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'El :attribute debe de ser aceptado.',
    'active_url'           => 'El :attribute no es una URL válida.',
    'after'                => 'El :attribute debe de ser una fecha después de :date.',
    'alpha'                => 'El :attribute solo puede contener letras.',
    'alpha_dash'           => 'El :attribute solo puede contener letras, números, y guiones.',
    'alpha_num'            => 'El :attribute solo puede contener letras y números.',
    'array'                => 'El :attribute debe de ser un array.',
    'before'               => 'El :attribute debe de ser una fecha antes de :date.',
    'between'              => [
        'numeric' => 'El :attribute debe de estar entre :min y :max.',
        'file'    => 'El :attribute debe de estar entre :min y :max kilobytes.',
        'string'  => 'El :attribute debe de estar entre :min y :max carácteres.',
        'array'   => 'El :attribute debe de tener entre :min y :max items.',
    ],
    'boolean'              => 'El campo :attribute debe de ser true o false.',
    'confirmed'            => 'El :attribute confirmación no coincide.',
    'date'                 => 'El :attribute no es una fecha válida.',
    'date_format'          => 'El :attribute no coincide con el formato :format.',
    'different'            => 'El :attribute y :other deben de ser diferentes.',
    'digits'               => 'El :attribute debe de contener :digits dígitos.',
    'digits_between'       => 'El :attribute debe de estar entre :min y :max dígitos.',
    'email'                => 'El :attribute debe de tener una cuenta de email válida.',
    'filled'               => 'El :attribute field es necesario.',
    'exists'               => 'El selected :attribute es inválido.',
    'image'                => 'El :attribute debe de ser una imagen.',
    'in'                   => 'El :attribute seleccionado es inválido.',
    'integer'              => 'El :attribute debe de ser un número.',
    'ip'                   => 'El :attribute de ser una IP válida.',
    'max'                  => [
        'numeric' => 'El :attribute no puede ser mayor que :max.',
        'file'    => 'El :attribute no puede ser mayor que :max kilobytes.',
        'string'  => 'El :attribute no puede ser mayor que :max carácteres.',
        'array'   => 'El :attribute no puede tener más de :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'El :attribute debe de tener como mínimo :min.',
        'file'    => 'El :attribute debe de tener como mínimo :min kilobytes.',
        'string'  => 'El :attribute debe de tener como mínimo :min carácteres.',
        'array'   => 'El :attribute debe de tener como mínimo :min items.',
    ],
    'not_in'               => 'El :attribute seleccionado es inválido.',
    'numeric'              => 'El :attribute debe de ser un número.',
    'regex'                => 'El formato de :attribute es inválido.',
    'required'             => 'El campo :attribute es requerido.',
    'required_if'          => 'El campo :attribute es requerido cuando :other es :value.',
    'required_with'        => 'El campo :attribute es requerido cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es requerido cuando :values está presente.',
    'required_without'     => 'El campo :attribute es requerido cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es requerido cuando ninguno de los :values está presente.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => ':attribute debe ser de :size.',
        'file'    => ':attribute debe ser de :size kilobytes.',
        'string'  => ':attribute debe ser de :size characters.',
        'array'   => ':attribute debe contener :size items.',
    ],
    'string'               => ':attribute debe de ser una cadena de texto.',
    'timezone'             => ':attribute debe de ser una zona válida.',
    'unique'               => ':attribute ya ha sido usado.',
    'url'                  => 'El formato de :attribute es inválido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];

return \FI\Support\TranslationOverride::override(__FILE__, $translations);
