# ADR-0003: Modularer Monolith mit Lizenzgrenze als Modulgrenze

Status: akzeptiert (2026-07-08)

## Kontext

Das Geschäftsmodell verlangt einzeln buchbare Module. Alternativen: unstrukturierter
Monolith, Microservices, oder ein Monolith mit strikten internen Modulgrenzen.

## Entscheidung

Modularer Monolith. Fachmodule liegen unter `app-modules/` (Namespace `Modules\…`,
ein Composer-Autoload-Eintrag, keine `src/`-Zwischenebene). Jedes Modul hat einen
eigenen ServiceProvider und ein eigenes Filament-Plugin und ist zur Laufzeit pro
Tenant abschaltbar (Lizenz).

Kommunikationsregeln (verbindlich, siehe module-conventions.md):

- Module kennen einander nicht direkt — nur Events und veröffentlichte Contracts.
- Der Core (`app/`) kennt keine Module; Module dürfen den Core nutzen.

## Konsequenzen

- Die Codestruktur folgt dem Geschäftsmodell (buchbare Einheit = Modul).
- Kein Microservice-Betriebsaufwand (ein Deployment, eine Datenbank).
- Die Grenzen halten nur, wenn die Kommunikationsregeln konsequent eingehalten
  werden — Verstöße gelten als Fehler, nicht als Abkürzung.
- Einzelne Module können später extrahiert werden, falls je nötig.
