# ADR-0005: Trennung von User (Login) und Employee (Personalstammsatz)

Status: akzeptiert (2026-07-08)

## Kontext

Mitarbeiterdaten (Personalnummer, Stammstation, Eintrittsdatum) und Login-Konten
haben unterschiedliche Lebenszyklen: nicht jeder Mitarbeiter hat einen Login,
externe User (Steuerberater, Dienstleister) sind keine Mitarbeiter, und ein User
kann in mehreren Organizations Mitglied sein.

## Entscheidung

Zwei Tabellen:

- `users` — Authentifizierung, global; Mitgliedschaft über `organization_user`.
- `employees` — Personalstammsatz, gehört genau einer Organization;
  `user_id` ist optional (nullable).

Mitarbeiterstammdaten gehören zum **Core** (Phase 1), da Rollen-/Stationszuordnung
sie sofort braucht; Dienstplan und Zeiterfassung bleiben ein lizenzierbares Modul.

## Konsequenzen

- Steuerberater-Zugang, Mitarbeiter ohne Login und Mehrfach-Mitgliedschaft sind
  ohne Sonderfälle abbildbar.
- Fachmodule referenzieren `employee_id` (nicht `user_id`), wenn es um Personal
  geht, und `user_id` nur für „wer hat es im System getan" (Audit).
