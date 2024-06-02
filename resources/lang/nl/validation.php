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

    'accepted'             => 'Het :attribute moet worden geaccepteerd.',
    'active_url'           => 'Het :attribute is geen geldige URL.',
    'after'                => 'Het :attribute moet later zijn dan :date.',
	'after_or_equal'       => 'The :attribute moet een datum zijn later of gelijk aan :date.',
    'alpha'                => 'Het :attribute mag alleen letters bevatten.',
    'alpha_dash'           => 'Het :attribute mag alleen letters, cijfers en streepjes bevatten.',
    'alpha_num'            => 'Het :attribute mag alleen letters en cijfers bevatten.',
    'array'                => 'Het :attribute moet een array zijn.',
    'before'               => 'Het :attribute moet eerder zijn dan :date.',
	'before_or_equal'      => 'HEt :attribute moet eerder zijn of gelijk aan :date.',
    'between'              => [
        'numeric' => 'Het :attribute moet tussen :min - :max zijn.',
        'file'    => 'Het :attribute moet tussen :min - :max kilobytes zijn.',
        'string'  => 'Het :attribute moet tussen :min - :max karakters zijn.',
        'array'   => 'Het :attribute moet tussen :min - :max items zijn.',
    ],
    'boolean'              => 'Het :attribute veld moet waar of onwaar zijn.',
    'confirmed'            => 'Het :attribute bevestigingsveld komt niet overeen.',
    'date'                 => 'Het :attribute is geen geldige datum.',
    'date_format'          => 'Het :attribute komt niet overeen met het formaat :format.',
    'different'            => 'Het :attribute en :other moeten verschillen.',
    'digits'               => 'Het :attribute moet :digits tekens zijn.',
    'digits_between'       => 'Het :attribute tussen :min en :max tekens zijn.',
	'dimensions'           => 'Het :attribute heeft ongeldige afbeelding afmetingen',
	'distinct'             => 'Het :attribute veld heeft een dubbele waarde.',
    'email'                => 'Het :attribute moet een valide email adres zijn.',
	'exists'               => 'Het geselecteerde :attribute is ongeldig.',
	'file'                 => 'Het :attribute moet een bestand zijn.',
    'filled'               => 'Het :attribute veld is vereist.',
    'image'                => 'Het :attribute moet een afbeelding zijn.',
    'in'                   => 'Het geselecteerde :attribute is ongeldig.',
	'in_array'             => 'Het :attribute bestaat niet in :other.',
    'integer'              => 'Het :attribute moet een integer zijn.',
    'ip'                   => 'Het :attribute moet een geldig IP adres zijn.',
	'ipv4'				   => 'Het :attribute moet een geldig IPv4 adres zijn.',
	'ipv6'				   => 'Het :attribute moet een geldig IPv6 adres zijn.',
	'json'				   => 'Het :attribute moet een geldige JSON string zijn.',
    'max'                  => [
        'numeric' => 'Het :attribute mag niet groter zijn dan :max.',
        'file'    => 'Het :attribute mag niet groter zijn dan :max kilobytes.',
        'string'  => 'Het :attribute mag niet groter zijn dan :max karakters.',
        'array'   => 'Het :attribute mag niet meer bevatten dan :max items.',
    ],
    'mimes'                => 'Het :attribute moet een bestand zijn van type :values.',
	'mimetypes'			   => 'Het :attribute moet een bestand zijn van type :values.',
    'min'                  => [
        'numeric' => 'Het :attribute moet minimaal :min zijn.',
        'file'    => 'Het :attribute moet minimaal :min kilobytes zijn.',
        'string'  => 'Het :attribute moet minimaal :min karakters hebben.',
        'array'   => 'Het :attribute moet minimaal :min items hebben.',
    ],
    'not_in'               => 'Het geselecteerde :attribute is ongeldig.',
    'numeric'              => 'Het :attribute moet een cijfer zijn.',
	'present'			   => 'Het :attribute veld moet bestaan.',
    'regex'                => 'Het :attribute formaat is ongeldig.',
    'required'             => 'Het :attribute veld is vereist.',
    'required_if'          => 'Het :attribute veld is vereist wanneer :other is :value.',
	'required_unless'	   => 'Het :attribute veld is vereist tenzij :other is in :values.',
    'required_with'        => 'Het :attribute veld is vereist wanneer :values aanwezig is.',
    'required_with_all'    => 'Het :attribute veld is vereist wanneer :values aanwezig is.',
    'required_without'     => 'Het :attribute veld is vereist wanneer :values niet aanwezig is.',
    'required_without_all' => 'Het :attribute veld is vereist wanneer geen van de :values aanwezig zijn.',
    'same'                 => 'Het :attribute en :other moeten overeen komen.',
    'size'                 => [
        'numeric' => 'Het :attribute moet :size zijn.',
        'file'    => 'Het :attribute moet :size kilobytes zijn.',
        'string'  => 'Het :attribute moet :size karakters zijn.',
        'array'   => 'Het :attribute moet :size items bevatten.',
    ],
    'string'               => 'Het :attribute moet een tekenreeks zijn.',
    'timezone'             => 'Het :attribute moet een geldige zone zijn.',
    'unique'               => 'Het :attribute is al bezet.',
	'uploaded'			   => 'Het :attribute mislukte in uploaden.',
    'url'                  => 'Het :attribute formaat is ongeldig.',
	'captcha'			   => 'Het :attributeis foutief',

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
	
    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'Aangepast bericht',
        ],
        'field_label'    => [
            'regex' => 'Het veld label bevat een ongeldig teken. Toegestane tekens zijn: A-Z, a-z, 0-9, space, -, _',
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
