# ADR-0006: Datentypen für Geld, Mengen, Zeit und Identifier

Status: akzeptiert (2026-07-08)

## Kontext

Tankstellendaten enthalten Geldbeträge (inkl. 3. Nachkommastelle bei
Kraftstoffpreisen), Literangaben, Schichten über Mitternacht und öffentlich
sichtbare IDs. Float-Arithmetik und lokale Zeiten sind hier klassische Fehlerquellen.

## Entscheidung

- **Geldbeträge:** Integer in Cent (`BIGINT`), nie Float.
- **Kraftstoffpreise:** `DECIMAL(8,3)` €/Liter (Preis wie 1,859 €).
- **Mengen (Liter):** `DECIMAL(12,3)`.
- **Zeit:** Speicherung in UTC, Anzeige in Europe/Berlin; Zeiträume immer als
  `starts_at`/`ends_at`-Timestamps, nie als Datum + lokale Uhrzeit getrennt.
- **Identifier:** ULIDs als Primärschlüssel für fachliche Tabellen; keine
  fortlaufenden IDs in URLs oder der API (Rückschluss auf Geschäftsvolumen).
- **Unveränderlichkeit:** Abgeschlossene Abrechnungen werden nie editiert,
  sondern storniert und neu erstellt (GoBD-Grundsatz); Soft Deletes und
  Audit-Log auf allen fachlichen Kerntabellen.

## Konsequenzen

- Summen sind exakt reproduzierbar; keine Rundungsdrift.
- Schichten über Mitternacht und Zeitumstellungen sind korrekt abbildbar.
- Casts/Value Objects im Code kapseln die Cent-Darstellung, damit im UI immer
  Euro-Beträge erscheinen.
