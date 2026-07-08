<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

/**
 * Login-Konto. Mitgliedschaft in Organizations über organization_user (n:m);
 * der Personalstammsatz ist bewusst getrennt (Employee, ADR-0005).
 */
class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use HasUlids;
    use Notifiable;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'is_platform_admin',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribut-Casts des Models.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
        ];
    }

    /**
     * Organizations, in denen der Benutzer Mitglied ist.
     *
     * @return BelongsToMany<Organization, $this>
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withTimestamps();
    }

    /**
     * Stationen, auf die der Benutzer explizit eingeschränkt ist.
     * Keine Einträge = Zugriff auf alle Stationen seiner Organization.
     *
     * @return BelongsToMany<Station, $this>
     */
    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class)->withTimestamps();
    }

    /**
     * Personalstammsätze (max. einer je Organization).
     *
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Panel-Zugriff: /admin nur für Plattform-Administratoren (Betreiber),
     * /app für alle Benutzer mit mindestens einer Organization-Mitgliedschaft.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_platform_admin;
        }

        return true;
    }

    /**
     * Verfügbare Tenants für den Tenant-Umschalter im App-Panel.
     *
     * @return Collection<int, Organization>
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->organizations;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant instanceof Organization
            && $this->organizations()->whereKey($tenant->getKey())->exists();
    }
}
