<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmployeeStatus;
use App\Tenancy\Concerns\BelongsToTenant;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Personalstammsatz — bewusst getrennt vom Login-Konto (users), siehe ADR-0005.
 */
class Employee extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'station_id',
        'user_id',
        'personnel_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'hired_at',
        'terminated_at',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => EmployeeStatus::class,
            'birth_date' => 'date',
            'hired_at' => 'date',
            'terminated_at' => 'date',
        ];
    }

    /**
     * Stammstation des Mitarbeiters.
     *
     * @return BelongsTo<Station, $this>
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * Optionales Login-Konto.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim($this->first_name.' '.$this->last_name));
    }
}
