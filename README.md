# Pumpe — Modulare SaaS-Tankstellenverwaltung

Digitalisiert die Geschäftsprozesse von Tankstellen — für Einzelbetreiber und
Partner mit mehreren Standorten. Multi-Tenant, modular, mit Modul-/Lizenzsystem.

## Dokumentation

- [Architekturüberblick](docs/architecture.md)
- [Modul-Konventionen](docs/module-conventions.md)
- [Architektur-Entscheidungen (ADRs)](docs/adr/)

## Stack

Laravel 12 · Filament 5 · MySQL 8 · Pest · Larastan · Pint

## Entwicklung

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate

composer test       # Pest
composer analyse    # PHPStan (Larastan, Level 6)
composer format     # Pint
```

## Sprachregelung

Code, Datenbank und Klassennamen englisch — Kommentare, UI-Texte und
Dokumentation deutsch. UI-Texte ausschließlich über Sprachdateien (`lang/de`).
