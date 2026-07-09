<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Database\Factories;

use App\Models\Organization;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ShiftReconciliation\Enums\ShiftStatus;
use Modules\ShiftReconciliation\Models\Shift;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organization = Organization::factory();

        return [
            'organization_id' => $organization,
            'station_id' => Station::factory()->for($organization, 'organization'),
            'starts_at' => now()->subHours(8),
            'ends_at' => now(),
            'status' => ShiftStatus::Open,
        ];
    }
}
