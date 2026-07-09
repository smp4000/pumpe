<?php

declare(strict_types=1);

namespace Modules\ShiftReconciliation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ShiftReconciliation\Enums\PaymentMethod;
use Modules\ShiftReconciliation\Models\Shift;
use Modules\ShiftReconciliation\Models\ShiftEntry;

/**
 * @extends Factory<ShiftEntry>
 */
class ShiftEntryFactory extends Factory
{
    protected $model = ShiftEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $expected = fake()->numberBetween(10_000, 500_000);

        return [
            'shift_id' => Shift::factory(),
            'organization_id' => fn (array $attributes) => Shift::withoutTenancy()
                ->findOrFail($attributes['shift_id'])
                ->organization_id,
            'payment_method' => PaymentMethod::Cash,
            'expected_amount_cents' => $expected,
            'counted_amount_cents' => $expected,
        ];
    }
}
