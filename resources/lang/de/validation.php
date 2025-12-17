<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Diese Sprachzeilen enthalten die Standard-Fehlermeldungen, die von der
    | Validator-Klasse verwendet werden. Einige Regeln haben mehrere Versionen,
    | z.B. die Größenregeln. Du kannst diese Meldungen hier nach Belieben anpassen.
    |
    */

    'accepted'             => 'Das :attribute muss akzeptiert werden.',
    'active_url'           => 'Das :attribute ist keine gültige URL.',
    'after'                => 'Das :attribute muss ein Datum nach :date sein.',
    'alpha'                => 'Das :attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => 'Das :attribute darf nur Buchstaben, Zahlen und Bindestriche enthalten.',
    'alpha_num'            => 'Das :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array'                => 'Das :attribute muss ein Array sein.',
    'before'               => 'Das :attribute muss ein Datum vor :date sein.',
    'between'              => [
        'numeric' => 'Das :attribute muss zwischen :min und :max liegen.',
        'file'    => 'Das :attribute muss zwischen :min und :max Kilobytes groß sein.',
        'string'  => 'Das :attribute muss zwischen :min und :max Zeichen haben.',
        'array'   => 'Das :attribute muss zwischen :min und :max Elemente haben.',
    ],
    'boolean'              => 'Das :attribute Feld muss true oder false sein.',
    'confirmed'            => 'Die :attribute Bestätigung stimmt nicht überein.',
    'date'                 => 'Das :attribute ist kein gültiges Datum.',
    'date_format'          => 'Das :attribute entspricht nicht dem Format :format.',
    'different'            => 'Das :attribute und :other müssen unterschiedlich sein.',
    'digits'               => 'Das :attribute muss :digits Ziffern haben.',
    'digits_between'       => 'Das :attribute muss zwischen :min und :max Ziffern haben.',
    'dimensions'           => 'Das :attribute hat ungültige Bilddimensionen.',
    'distinct'             => 'Das :attribute Feld hat einen doppelten Wert.',
    'email'                => 'Das :attribute muss eine gültige E-Mail-Adresse sein.',
    'exists'               => 'Das ausgewählte :attribute ist ungültig.',
    'file'                 => 'Das :attribute muss eine Datei sein.',
    'filled'               => 'Das :attribute Feld ist erforderlich.',
    'image'                => 'Das :attribute muss ein Bild sein.',
    'in'                   => 'Das ausgewählte :attribute ist ungültig.',
    'in_array'             => 'Das :attribute Feld existiert nicht in :other.',
    'integer'              => 'Das :attribute muss eine ganze Zahl sein.',
    'ip'                   => 'Das :attribute muss eine gültige IP-Adresse sein.',
    'json'                 => 'Das :attribute muss ein gültiger JSON-String sein.',
    'max'                  => [
        'numeric' => 'Das :attribute darf nicht größer als :max sein.',
        'file'    => 'Das :attribute darf nicht größer als :max Kilobytes sein.',
        'string'  => 'Das :attribute darf nicht länger als :max Zeichen sein.',
        'array'   => 'Das :attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes'                => 'Das :attribute muss eine Datei des Typs :values sein.',
    'mimetypes'            => 'Das :attribute muss eine Datei des Typs :values sein.',
    'min'                  => [
        'numeric' => 'Das :attribute muss mindestens :min sein.',
        'file'    => 'Das :attribute muss mindestens :min Kilobytes groß sein.',
        'string'  => 'Das :attribute muss mindestens :min Zeichen haben.',
        'array'   => 'Das :attribute muss mindestens :min Elemente haben.',
    ],
    'not_in'               => 'Das ausgewählte :attribute ist ungültig.',
    'numeric'              => 'Das :attribute muss eine Zahl sein.',
    'present'              => 'Das :attribute Feld muss vorhanden sein.',
    'regex'                => 'Das :attribute Format ist ungültig.',
    'required'             => 'Das :attribute Feld ist erforderlich.',
    'required_if'          => 'Das :attribute Feld ist erforderlich, wenn :other :value ist.',
    'required_unless'      => 'Das :attribute Feld ist erforderlich, außer :other ist in :values.',
    'required_with'        => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all'    => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden sind.',
    'required_without'     => 'Das :attribute Feld ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das :attribute Feld ist erforderlich, wenn keines der :values vorhanden ist.',
    'same'                 => 'Das :attribute und :other müssen übereinstimmen.',
    'size'                 => [
        'numeric' => 'Das :attribute muss :size sein.',
        'file'    => 'Das :attribute muss :size Kilobytes groß sein.',
        'string'  => 'Das :attribute muss :size Zeichen haben.',
        'array'   => 'Das :attribute muss :size Elemente enthalten.',
    ],
    'string'               => 'Das :attribute muss ein String sein.',
    'timezone'             => 'Das :attribute muss eine gültige Zeitzone sein.',
    'unique'               => 'Das :attribute wurde bereits vergeben.',
    'uploaded'             => 'Das :attribute konnte nicht hochgeladen werden.',
    'url'                  => 'Das :attribute Format ist ungültig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Hier kannst du eigene Validierungsnachrichten für Attribute angeben
    | mit der Konvention "attribute.rule". So lässt sich schnell eine
    | individuelle Meldung für eine bestimmte Regel erstellen.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'benutzerdefinierte-nachricht',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Diese Sprachzeilen werden verwendet, um Platzhalter wie E-Mail-Adresse
    | durch leserfreundlichere Begriffe zu ersetzen.
    |
    */

    'attributes' => [],

];
