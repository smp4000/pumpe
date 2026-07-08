# ADR-0001: Multi-Tenancy mit gemeinsamer Datenbank

Status: akzeptiert (2026-07-08)

## Kontext

Die Software wird von vielen kleinen bis mittleren Mandanten (Tankstellen/Partner)
genutzt. Alternativen: gemeinsame Datenbank mit `tenant_id`-Spalte vs. eine
Datenbank pro Mandant.

## Entscheidung

Gemeinsame Datenbank; jede mandantenbezogene Tabelle trägt `organization_id`.
Filament-natives Multi-Tenancy wird genutzt, kein zusätzliches Tenancy-Paket
(zwei parallele Tenancy-Systeme sind eine bekannte Fehlerquelle).

Die Trennung wird technisch erzwungen:

- `BelongsToTenant`-Trait mit Global Scope filtert jede Query automatisch,
- `organization_id` wird beim Erstellen automatisch gesetzt,
- Zugriffe ohne Tenant-Kontext werfen eine Exception (fail-closed),
- Feature-Tests beweisen die Isolation für jede Modultabelle.

## Konsequenzen

- Migrationen laufen einmal, Onboarding ist ein Insert, mandantenübergreifende
  Auswertungen (Betreiber-Panel) sind trivial.
- Die Isolation ist logisch, nicht physisch — daher die fail-closed-Mechanik und
  Pflicht-Tests. Datenexport pro Mandant (DSGVO) muss über `organization_id`
  selektieren.
- Bei künftigen Großkunden mit Sonderanforderungen bleibt eine dedizierte
  Instanz (eigene Deployment-Umgebung) als Ausweichoption.
