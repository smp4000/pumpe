<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ModuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Datenbank-Abbild eines Moduls. Quelle der Wahrheit ist das
 * module.json-Manifest — Abgleich über `php artisan modules:sync`.
 *
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property bool $is_core
 */
class Module extends Model
{
    /** @use HasFactory<ModuleFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_core',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
        ];
    }

    /**
     * @return HasMany<ModuleLicense, $this>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(ModuleLicense::class);
    }
}
