# Modul-Konventionen

Diese Konventionen sind verbindlich für alle Fachmodule unter `app-modules/`.
Abweichungen nur mit dokumentierter Begründung (neues ADR).

## Struktur

Namespace-Wurzel eines Moduls ist der Modulordner selbst
(Composer: `"Modules\\": "app-modules/"`). Kleingeschriebene Ordner enthalten
Nicht-Klassen-Dateien und kollidieren nicht mit PSR-4.

```
app-modules/
  ShiftReconciliation/              ← PascalCase, englisch, Singular-Begriff
    Providers/
      ShiftReconciliationServiceProvider.php   ← Pflicht, einziger Einstiegspunkt
    Filament/
      ShiftReconciliationPlugin.php            ← Filament-Plugin (Panel-Registrierung)
      Resources/  Pages/  Widgets/
    Http/Api/V1/
      Controllers/  Requests/  Resources/
    Models/
    Actions/                        ← Geschäftslogik, von UI und API gemeinsam genutzt
    Events/                         ← öffentliche Ereignisse des Moduls
    Listeners/
    Contracts/                      ← öffentliche Schnittstellen für andere Module
    Policies/
    Enums/
    Tests/
      Feature/  Unit/
    database/
      migrations/  factories/
    lang/de/
      shift-reconciliation.php
    routes/
      api.php
    module.json                     ← Manifest
```

## Manifest (`module.json`)

```json
{
    "code": "shift-reconciliation",
    "name": "Schichtabrechnung",
    "description": "Kassen- und Schichtabrechnung mit Soll/Ist-Abgleich.",
    "is_core": false,
    "depends_on": []
}
```

- `code` ist der stabile, kebab-case Identifier — er erscheint in der Lizenztabelle,
  in Permission-Namen und in Übersetzungs-Keys. Er wird **nie** geändert.
- `depends_on` listet Modul-Codes, die aktiv sein müssen.

## Regeln

1. **Grenzen:** Ein Modul greift niemals direkt auf Models, Tabellen oder Klassen
   eines anderen Moduls zu. Erlaubte Kopplung:
   - eigene **Events** dispatchen, fremde Events konsumieren,
   - fremde **Contracts** (Interfaces) benutzen, die das andere Modul unter
     `Contracts/` veröffentlicht und in seinem ServiceProvider bindet.
   - Core-Klassen (`App\…`) dürfen von jedem Modul benutzt werden; der Core kennt
     umgekehrt **keine** Module.
2. **Geschäftslogik in Actions:** Filament-Resources und API-Controller enthalten
   keine Geschäftslogik — sie validieren, rufen eine Action auf, präsentieren das
   Ergebnis. Jede Action ist einzeln testbar.
3. **Permissions:** Schema `<module-code>.<resource>.<ability>`, z. B.
   `shift-reconciliation.shifts.close`. Registrierung im ServiceProvider;
   der Core synchronisiert sie in die Datenbank.
4. **Lizenzprüfung:** Das Filament-Plugin wird nur bei aktiver Lizenz des aktuellen
   Tenants registriert; `routes/api.php` steht unter der Middleware
   `module:<module-code>`; Policies prüfen zusätzlich.
5. **API:** Alle Endpunkte unter `/api/v1/<module-code>/…`, Responses ausschließlich
   über API Resources, Eingaben über Form Requests. Jede Änderung wird in der
   OpenAPI-Spezifikation nachgezogen (Vertrag mit der Flutter-App).
6. **Migrationen:** Tabellennamen mit Modulpräfix, z. B. `shift_reconciliation_shifts`.
   Operative Tabellen tragen `organization_id` und `station_id` und nutzen den
   `BelongsToTenant`-Trait.
7. **Sprache:** Alle UI-Texte über `lang/de/<module-code>.php`. Kein deutscher Text
   im PHP-Code außerhalb von Sprachdateien und Kommentaren.
8. **Tests:** Jedes Modul bringt Feature-Tests für seine Kern-Flows mit, darunter
   mindestens einen Test, der die **Tenant-Isolation** der Modultabellen beweist
   (Tenant A sieht keine Daten von Tenant B).
9. **Ein Modul, ein Zweck:** Wenn ein Modul zwei unabhängig lizenzierbare Funktionen
   enthält, sind es zwei Module.
