# Module

Dieses Verzeichnis enthält alle Fachmodule der Anwendung. Jedes Modul ist eine in sich
geschlossene Einheit mit eigenem ServiceProvider, eigenem Filament-Plugin, eigenen
Migrationen, Sprachdateien und Tests.

Die verbindlichen Konventionen (Struktur, Namensgebung, Kommunikationsregeln zwischen
Modulen) sind in [docs/module-conventions.md](../docs/module-conventions.md) beschrieben.

Kurzfassung der Struktur eines Moduls (Namespace `Modules\<ModulName>`):

```
app-modules/
  ShiftReconciliation/          ← PascalCase, englisch
    Providers/                  ← ServiceProvider (Pflicht)
    Filament/                   ← Filament-Plugin, Resources, Pages, Widgets
    Http/Api/V1/                ← API-Controller, Requests, Resources
    Models/
    Actions/                    ← Geschäftslogik (von UI und API gemeinsam genutzt)
    Events/
    Contracts/                  ← öffentliche Schnittstellen für andere Module
    Policies/
    Tests/
      Feature/
      Unit/
    database/
      migrations/
      factories/
    lang/de/
    routes/
      api.php
    module.json                 ← Modul-Manifest (Code, Name, Abhängigkeiten)
```
