# Architekturüberblick

Modulare SaaS-Tankstellenverwaltung für einzelne Tankstellen und Partner mit mehreren
Standorten. Zielmarkt zunächst Deutschland (GoBD, HACCP, Eichrecht relevant), später
ggf. DACH.

Verbindliche Grundsatzentscheidungen sind als ADRs unter [docs/adr/](adr/) dokumentiert.
Die Modul-Konventionen stehen in [module-conventions.md](module-conventions.md).

## Systemkontext

- Verwaltungssoftware **neben** dem Kassensystem — keine POS-/TSE-Funktionalität.
- Datenübernahme aus Kassensystemen ausschließlich per **CSV-Import** über ein
  zentrales Import-Framework (Profile pro Hersteller, Vorschau, Fehlerbericht,
  idempotente Wiederholung).
- Mobile Erfassung später über eine **Flutter-Android-App** (separates Repository);
  Vertrag zwischen Web und App ist die **OpenAPI-Spezifikation** der REST-API.

## Technologie-Stack

| Ebene | Technologie |
| --- | --- |
| Framework | Laravel 12, PHP ≥ 8.2 (Ziel: 8.3+) |
| Admin-/Tenant-UI | Filament 5 (zwei Panels: `/admin` Betreiber, `/app` Mandanten) |
| Datenbank | MySQL 8 (Produktion); Achtung: XAMPP lokal liefert MariaDB |
| API | REST unter `/api/v1`, Sanctum-Token, API Resources |
| Tests / Qualität | Pest, Larastan (Level 6), Pint (strict_types), GitHub Actions |

## Mandantenmodell (zweistufig)

```
Organization  (Tenant, Vertragspartner, Lizenznehmer)
 └── Station  (Standort)  1..n
      └── operative Daten (Schichten, Bestände, Preise, Personal …)
```

- `organization_id` ist die Tenant-Grenze und steht in praktisch jeder Tabelle
  (Shared Database, siehe ADR-0001/0002).
- Operative Tabellen tragen zusätzlich `station_id`.
- Tenant-Scoping erfolgt automatisch über einen `BelongsToTenant`-Trait mit Global
  Scope. Schreibzugriffe ohne Tenant-Kontext schlagen fehl (fail-closed).
- User ↔ Organization ist eine n:m-Beziehung (`organization_user`), Stationszugriff
  wird über `station_user` eingeschränkt (keine Einträge = alle Stationen).

## Schichtenmodell

Geschäftslogik lebt in **Actions/Services** — niemals in Filament-Klassen oder
Controllern. Filament-UI und API-Controller sind austauschbare Aufrufer derselben
Logik:

```
Filament (Panels)   REST-API (/api/v1)
        \                 /
         Actions / Services      ← Geschäftslogik, Transaktionen, Events
                |
         Models (Eloquent)       ← Tenant-Scopes, Casts, Relationen
                |
             MySQL
```

## Modul- und Lizenzsystem

- Jedes Fachmodul ist ein Paket unter `app-modules/` (Namespace `Modules\…`) mit
  eigenem ServiceProvider, Filament-Plugin, Migrationen, Routen, Sprachdateien
  und Tests (Details: module-conventions.md).
- Der Core (Tenancy, Identity, Lizenz, Import, Audit, Settings, Notifications)
  ist **kein** Modul, sondern liegt unter `app/`.
- Lizenzprüfung dreistufig (Verteidigung in der Tiefe):
  1. **Navigation** — Filament-Plugin des Moduls wird nur bei aktiver Lizenz registriert,
  2. **Middleware** — Routen/API-Endpunkte des Moduls prüfen die Lizenz,
  3. **Policy** — Datenzugriff prüft Lizenz und Berechtigung.
- Module kommunizieren untereinander ausschließlich über **Events und Contracts**,
  nie über direkte Model-Zugriffe in fremde Module (ADR-0003).

## Querschnittsregeln

- **Sprache:** Code, Tabellen, Klassen englisch; Kommentare, UI, Doku deutsch.
  Alle UI-Texte über Sprachdateien (`lang/de`), nie hartcodiert.
- **Geld:** Integer-Cents; Kraftstoffpreise mit 3 Nachkommastellen (`DECIMAL(8,3)`),
  Mengen in Litern als `DECIMAL(12,3)` — niemals Float (ADR-0006).
- **Zeit:** Speicherung UTC, Anzeige Europe/Berlin.
- **IDs:** ULIDs für öffentlich sichtbare Identifier (API, URLs).
- **Nachvollziehbarkeit:** Audit-Log auf allen fachlichen Kerntabellen, Soft Deletes;
  abgeschlossene Abrechnungen sind unveränderlich (Storno statt Änderung, GoBD).
- **Asynchronität:** Reports, Exporte, Importe und Benachrichtigungen laufen über
  Queues, nie synchron im Request.

## Entwicklungsphasen

| Phase | Inhalt | Status |
| --- | --- | --- |
| 0 | Fundament: Git, Laravel 12, Tooling, CI, Modul-Gerüst, Doku | in Arbeit |
| 1 | Core: Organizations, Stationen, User/Einladungen, Mitarbeiter, Rollen/Rechte, Panels | offen |
| 2 | Modul-/Lizenzsystem, manuelle Freischaltung, CSV-Import-Framework | offen |
| 3 | Fachmodul Schichtabrechnung (Referenzimplementierung inkl. API v1 + OpenAPI) | offen |
| 4 | Kraftstoff-Bestandsführung | offen |
| 5 | Preisverwaltung, Prüfkalender, Checklisten | offen |
| 6 | Dienstplan/Zeiterfassung, danach Flutter-App | offen |
| 7+ | SaaS-Billing (Stripe/PayPal), Warenwirtschaft, DATEV-Export | offen |
