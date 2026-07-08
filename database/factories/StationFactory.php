<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StationStatus;
use App\Models\Organization;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Station>
 */
class StationFactory extends Factory
{
    protected $model = Station::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => 'Station '.fake()->unique()->city(),
            'station_number' => (string) fake()->unique()->numberBetween(1000, 9999),
            'street' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country_code' => 'DE',
            'timezone' => 'Europe/Berlin',
            'status' => StationStatus::Active,
        ];
    }
}
