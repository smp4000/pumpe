<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lizenzzuordnung Module ↔ Organizations. Abrechnung (Subscriptions,
     * Rechnungen, Zahlungen) dockt später an diese Tabelle an, ohne das
     * Lizenzmodell zu verändern (siehe Architekturüberblick).
     */
    public function up(): void
    {
        Schema::create('module_licenses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            // trial | active | cancelled | expired
            $table->string('status', 20)->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            // Bei Kündigung: Lizenz bleibt bis zu diesem Zeitpunkt nutzbar
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_licenses');
    }
};
