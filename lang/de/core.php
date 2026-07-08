<?php

declare(strict_types=1);

/*
 * Deutsche UI-Texte des Cores (Tenancy, Stammdaten, Rollen).
 */
return [
    'organization_status' => [
        'active' => 'Aktiv',
        'suspended' => 'Gesperrt',
    ],

    'station_status' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
    ],

    'employee_status' => [
        'active' => 'Aktiv',
        'inactive' => 'Ruhend',
        'terminated' => 'Ausgeschieden',
    ],

    'tenancy' => [
        'register_organization' => 'Neuen Betrieb anlegen',
        'organization_suspended' => 'Dieser Betrieb ist gesperrt. Bitte wenden Sie sich an den Support.',
    ],

    'fields' => [
        'organization_name' => 'Name des Betriebs',
        'organization_name_help' => 'Ihr Unternehmens- oder Betriebsname, z. B. „Tankstelle Müller GmbH".',
        'first_station_name' => 'Name der ersten Station',
        'first_station_name_help' => 'Weitere Standorte können Sie jederzeit ergänzen.',
        'name' => 'Name',
        'station_number' => 'Stationsnummer',
        'street' => 'Straße und Hausnummer',
        'postal_code' => 'PLZ',
        'city' => 'Ort',
        'phone' => 'Telefon',
        'status' => 'Status',
        'station' => 'Station',
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'full_name' => 'Name',
        'personnel_number' => 'Personalnummer',
        'email' => 'E-Mail-Adresse',
        'birth_date' => 'Geburtsdatum',
        'hired_at' => 'Eintrittsdatum',
        'terminated_at' => 'Austrittsdatum',
        'notes' => 'Notizen',
    ],

    'resources' => [
        'station' => [
            'label' => 'Station',
            'plural' => 'Stationen',
        ],
        'employee' => [
            'label' => 'Mitarbeiter',
            'plural' => 'Mitarbeiter',
        ],
    ],

    'nav' => [
        'master_data' => 'Stammdaten',
    ],

    'roles' => [
        'owner' => 'Inhaber',
        'station_manager' => 'Stationsleiter',
        'employee' => 'Mitarbeiter',
        'accounting' => 'Buchhaltung',
    ],
];
