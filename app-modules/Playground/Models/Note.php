<?php

declare(strict_types=1);

namespace Modules\Playground\Models;

use App\Models\Station;
use App\Tenancy\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Playground\Database\Factories\NoteFactory;

/**
 * Beispiel-Model des Referenzmoduls: tenant- und stationsbezogen,
 * Tabellenname mit Modulpräfix.
 *
 * @property string $title
 * @property string|null $body
 */
class Note extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<NoteFactory> */
    use HasFactory;

    use HasUlids;

    protected $table = 'playground_notes';

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'station_id',
        'title',
        'body',
    ];

    protected static function newFactory(): NoteFactory
    {
        return NoteFactory::new();
    }

    /**
     * @return BelongsTo<Station, $this>
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
