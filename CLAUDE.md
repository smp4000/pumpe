# Pumpe — Hinweise für die Entwicklung

Modulare SaaS-Tankstellenverwaltung. Laravel 12, Filament 5, MySQL, Pest, Larastan (Level 6), Pint.

## Pflichtlektüre vor Änderungen

- `docs/architecture.md` — Architekturüberblick und Phasenplan
- `docs/module-conventions.md` — verbindliche Modul-Konventionen
- `docs/adr/` — Grundsatzentscheidungen (nicht ohne neues ADR umstoßen)

## Kernregeln

- Code/DB/Klassen englisch; Kommentare, UI-Texte, Doku deutsch. UI-Texte nur über `lang/de`.
- Tenancy: `organization_id` (Tenant) + `station_id` auf operativen Tabellen; `BelongsToTenant`-Trait, fail-closed.
- Geschäftslogik in Actions/Services — nie in Filament-Klassen oder Controllern; UI und API rufen dieselben Actions.
- Module unter `app-modules/` (`Modules\…`), Kommunikation nur über Events/Contracts; Core (`app/`) kennt keine Module.
- Geld als Integer-Cent, Kraftstoffpreise `DECIMAL(8,3)`, Liter `DECIMAL(12,3)`, Zeiten UTC, ULIDs als fachliche PKs.
- Jede API-Änderung in der OpenAPI-Spec nachziehen (Vertrag mit der Flutter-App).

## Qualitäts-Gates (müssen vor jedem Commit grün sein)

```bash
composer format && composer analyse && composer test
```
