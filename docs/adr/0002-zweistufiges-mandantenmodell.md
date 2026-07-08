# ADR-0002: Zweistufiges Mandantenmodell (Organization → Station)

Status: akzeptiert (2026-07-08)

## Kontext

Zielgruppe sind einzelne Tankstellen **und** Partner mit mehreren Standorten.
Ein einstufiges Modell (Tenant = Tankstelle) würde Mehrstandort-Betreiber
ausschließen oder später einen Umbau jeder Tabelle erzwingen.

## Entscheidung

- `Organization` ist der Tenant: Vertragspartner, Lizenznehmer, Rechnungsempfänger.
- `Station` ist der Standort; eine Organization hat 1..n Stationen.
- Die einzelne Tankstelle ist eine Organization mit genau einer Station —
  kein Sonderfall im Code.
- Operative Tabellen tragen `organization_id` **und** `station_id`.
- Stationszugriff pro User über `station_user` (keine Einträge = alle Stationen).

## Konsequenzen

- Mehrstandort ist von Tag 1 an möglich, ohne dass Einzelbetreiber Komplexität
  im UI sehen (Stationsauswahl wird bei genau einer Station ausgeblendet).
- Jedes Fachmodul muss bei der Modellierung die Stationsebene mitdenken.
