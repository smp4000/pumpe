<?php

declare(strict_types=1);

namespace App\Modules;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Eingelesenes module.json-Manifest eines Moduls unter app-modules/.
 */
final readonly class ModuleManifest
{
    /**
     * @param  list<string>  $dependsOn  Modul-Codes, die aktiv sein müssen
     * @param  list<string>  $permissions  Berechtigungen des Moduls
     */
    public function __construct(
        public string $code,
        public string $name,
        public string $description,
        public bool $isCore,
        public array $dependsOn,
        public array $permissions,
        public string $directory,
    ) {}

    public static function fromFile(string $path): self
    {
        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data) || ! isset($data['code'], $data['name'])) {
            throw new InvalidArgumentException("Ungültiges Modul-Manifest: {$path}");
        }

        return new self(
            code: (string) $data['code'],
            name: (string) $data['name'],
            description: (string) ($data['description'] ?? ''),
            isCore: (bool) ($data['is_core'] ?? false),
            dependsOn: array_values((array) ($data['depends_on'] ?? [])),
            permissions: array_values((array) ($data['permissions'] ?? [])),
            directory: dirname($path),
        );
    }

    /**
     * PHP-Namespace des Moduls, abgeleitet aus dem Verzeichnisnamen.
     */
    public function namespace(): string
    {
        return 'Modules\\'.basename($this->directory);
    }

    /**
     * Konventionsbasierte Klassennamen des Moduls.
     */
    public function serviceProviderClass(): string
    {
        return $this->namespace().'\\Providers\\'.Str::studly(basename($this->directory)).'ServiceProvider';
    }

    public function filamentPluginClass(): string
    {
        return $this->namespace().'\\Filament\\'.Str::studly(basename($this->directory)).'Plugin';
    }
}
