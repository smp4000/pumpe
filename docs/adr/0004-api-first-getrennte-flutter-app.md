# ADR-0004: API-first; Flutter-App als separates Repository

Status: akzeptiert (2026-07-08)

## Kontext

Alle Mitarbeiter sollen Daten erfassen; dafür kommt eine Android-App (Flutter) auf
einem vorhandenen MDE-Gerät. Web (Laravel) und App (Flutter) werden getrennt
entwickelt und getrennt versioniert.

## Entscheidung

- Zwei Repositories: `pumpe` (Laravel/Web) und die Flutter-App.
- Die REST-API (`/api/v1`) entsteht **pro Modul sofort mit**, nicht nachträglich —
  Geschäftslogik liegt in Actions, die von Filament und API gemeinsam genutzt werden.
- Der Vertrag zwischen beiden Repos ist eine **OpenAPI-Spezifikation**, gepflegt im
  Laravel-Repo; die App generiert daraus ihre Client-Modelle.
- Authentifizierung über Sanctum-Tokens; API-Versionierung über den URL-Pfad.

## Konsequenzen

- Kein Logik-Duplikat zwischen Web und App; die App kann später ohne API-Nacharbeit
  starten.
- Jede API-Änderung erfordert das Nachziehen der OpenAPI-Spec (CI prüft das später).
- Offener Punkt: Android-Version des MDE-Geräts klären (Flutter benötigt API 21+).
