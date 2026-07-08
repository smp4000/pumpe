<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StationStatus;
use App\Tenancy\Concerns\BelongsToTenant;
use Database\Factories\StationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Standort (Tankstelle) einer Organization. Operative Daten der Fachmodule
 * hängen immer an einer Station.
 */
class Station extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<StationFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'name',
        'station_number',
        'street',
        'postal_code',
        'city',
        'country_code',
        'phone',
        'timezone',
        'status',
        'settings',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StationStatus::class,
            'settings' => 'array',
        ];
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Benutzer mit explizitem Zugriff auf diese Station.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
