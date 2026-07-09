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
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort bestätigen',
        'role' => 'Rolle',
        'roles' => 'Rollen',
        'expires_at' => 'Gültig bis',
        'created_at' => 'Erstellt am',
        'member_since' => 'Mitglied seit',
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
        'member' => [
            'label' => 'Mitglied',
            'plural' => 'Mitglieder',
        ],
        'invitation' => [
            'label' => 'Einladung',
            'plural' => 'Einladungen',
        ],
        'organization' => [
            'label' => 'Betrieb',
            'plural' => 'Betriebe',
        ],
    ],

    'nav' => [
        'master_data' => 'Stammdaten',
        'team' => 'Team',
    ],

    'tabs' => [
        'master_data' => 'Stammdaten',
        'address_contact' => 'Adresse & Kontakt',
        'person' => 'Person',
        'contact' => 'Kontakt',
        'employment' => 'Beschäftigung',
        'status' => 'Status',
    ],

    'invitations' => [
        'mail_subject' => 'Einladung zu :organization',
        'mail_greeting' => 'Guten Tag,',
        'mail_intro' => 'Sie wurden eingeladen, dem Betrieb „:organization" in Pumpe beizutreten.',
        'mail_button' => 'Einladung annehmen',
        'mail_expiry' => 'Der Link ist gültig bis :date Uhr.',
        'mail_salutation' => 'Viele Grüße, Ihr Pumpe-Team',
        'accept_title' => 'Einladung annehmen',
        'accept_intro' => 'Sie wurden zu „:organization" eingeladen. Erstellen Sie ein Konto für :email, um beizutreten.',
        'accept_button' => 'Konto erstellen und beitreten',
        'accept_footer' => 'Sie haben diese Einladung nicht erwartet? Dann können Sie diese Seite einfach schließen.',
        'expired_title' => 'Einladung nicht mehr gültig',
        'expired_text' => 'Diese Einladung wurde bereits angenommen oder ist abgelaufen. Bitten Sie den Absender, Ihnen eine neue Einladung zu schicken.',
        'mismatch_title' => 'Falsches Konto angemeldet',
        'mismatch_text' => 'Die Einladung gilt für :invited, Sie sind aber als :current angemeldet.',
        'mismatch_hint' => 'Bitte melden Sie sich ab und öffnen Sie den Einladungslink erneut.',
        'status_pending' => 'Offen',
        'status_accepted' => 'Angenommen',
        'status_expired' => 'Abgelaufen',
        'resend' => 'Erneut senden',
        'resent' => 'Die Einladung wurde erneut versendet.',
        'invited' => 'Die Einladung wurde versendet.',
    ],

    'members' => [
        'remove' => 'Aus dem Betrieb entfernen',
        'remove_confirm' => 'Soll dieses Mitglied wirklich aus dem Betrieb entfernt werden? Der Personalstammsatz bleibt erhalten.',
        'removed' => 'Das Mitglied wurde entfernt.',
        'cannot_remove_self' => 'Sie können sich nicht selbst entfernen.',
        'edit_roles' => 'Rollen bearbeiten',
    ],

    'license_status' => [
        'trial' => 'Testphase',
        'active' => 'Aktiv',
        'cancelled' => 'Gekündigt',
        'expired' => 'Beendet',
    ],

    'licenses' => [
        'title' => 'Lizenzen',
        'module' => 'Modul',
        'trial_ends_at' => 'Testphase bis',
        'expires_at' => 'Lizenz bis',
        'add' => 'Modul buchen',
    ],

    'modules' => [
        'not_licensed' => 'Dieses Modul ist für Ihren Betrieb nicht freigeschaltet.',
    ],

    'organizations' => [
        'suspend' => 'Sperren',
        'activate' => 'Entsperren',
        'suspended' => 'Der Betrieb wurde gesperrt.',
        'activated' => 'Der Betrieb wurde entsperrt.',
        'stations_count' => 'Stationen',
        'users_count' => 'Mitglieder',
    ],

    'roles' => [
        'owner' => 'Inhaber',
        'station_manager' => 'Stationsleiter',
        'employee' => 'Mitarbeiter',
        'accounting' => 'Buchhaltung',
    ],
];
