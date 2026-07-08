<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stationen sind die Standorte einer Organization. Operative Daten
     * (Schichten, Bestände, Preise …) hängen immer an einer Station.
     */
    public function up(): void
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // Interne Stationsnummer des Betreibers (z. B. Gesellschafts-Nr.)
            $table->string('station_number', 50)->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city')->nullable();
            $table->string('country_code', 2)->default('DE');
            $table->string('phone', 30)->nullable();
            $table->string('timezone', 40)->default('Europe/Berlin');
            // active | inactive (z. B. Standort geschlossen/umgebaut)
            $table->string('status', 20)->default('active');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
