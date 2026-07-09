<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LicenseStatus;
use App\Models\Module;
use App\Models\ModuleLicense;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModuleLicense>
 */
class ModuleLicenseFactory extends Factory
{
    protected $model = ModuleLicense::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'module_id' => Module::factory(),
            'status' => LicenseStatus::Active,
            'activated_at' => now(),
        ];
    }

    public function trial(): static
    {
        return $this->state([
            'status' => LicenseStatus::Trial,
            'trial_ends_at' => now()->addDays(30),
            'activated_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'status' => LicenseStatus::Expired,
            'expires_at' => now()->subDay(),
        ]);
    }
}
