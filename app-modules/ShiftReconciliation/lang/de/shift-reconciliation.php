<?php

declare(strict_types=1);

/*
 * Deutsche UI-Texte des Moduls Schichtabrechnung.
 */
return [
    'shifts' => [
        'label' => 'Schichtabrechnung',
        'plural' => 'Schichtabrechnungen',
    ],

    'nav_group' => 'Kasse',

    'status' => [
        'open' => 'Offen',
        'submitted' => 'Eingereicht',
        'approved' => 'Freigegeben',
        'cancelled' => 'Storniert',
    ],

    'payment_method' => [
        'cash' => 'Bargeld',
        'debit_card' => 'EC-Karte',
        'credit_card' => 'Kreditkarte',
        'voucher' => 'Gutschein',
        'other' => 'Sonstiges',
    ],

    'tabs' => [
        'shift' => 'Schichtdaten',
        'counting' => 'Zählung',
        'closing' => 'Abschluss',
    ],

    'fields' => [
        'starts_at' => 'Schichtbeginn',
        'ends_at' => 'Schichtende',
        'employee' => 'Schichtverantwortlicher',
        'entries' => 'Soll/Ist je Zahlart',
        'payment_method' => 'Zahlart',
        'expected' => 'Soll (laut Kasse)',
        'counted' => 'Ist (gezählt)',
        'difference' => 'Differenz',
        'note' => 'Anmerkung',
        'notes' => 'Bemerkungen zur Schicht',
        'submitted_at' => 'Eingereicht am',
        'approved_at' => 'Freigegeben am',
        'cancel_reason' => 'Storno-Begründung',
    ],

    'actions' => [
        'submit' => 'Einreichen',
        'submit_confirm' => 'Die Abrechnung wird zur Freigabe eingereicht und kann danach nicht mehr bearbeitet werden.',
        'submitted' => 'Die Abrechnung wurde eingereicht.',
        'approve' => 'Freigeben',
        'approve_confirm' => 'Nach der Freigabe ist die Abrechnung unveränderlich. Korrekturen sind nur noch per Storno möglich.',
        'approved' => 'Die Abrechnung wurde freigegeben.',
        'cancel' => 'Stornieren',
        'cancel_confirm' => 'Die Abrechnung wird storniert und bleibt als Beleg erhalten. Erstellen Sie anschließend eine korrigierte Abrechnung.',
        'cancelled' => 'Die Abrechnung wurde storniert.',
    ],
];
