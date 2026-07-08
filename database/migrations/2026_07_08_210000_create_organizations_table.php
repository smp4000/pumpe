<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Organizations sind die Mandanten (Tenants): Vertragspartner, Lizenznehmer
     * und Rechnungsempfänger. Jede mandantenbezogene Tabelle referenziert
     * organizations.id als Tenant-Grenze (siehe ADR-0001/0002).
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            // Slug für tenant-bezogene URLs im Filament-Panel (/app/{slug}/…)
            $table->string('slug')->unique();
            // Firmierung laut Handelsregister, falls abweichend vom Anzeigenamen
            $table->string('legal_name')->nullable();
            $table->string('vat_id', 20)->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city')->nullable();
            $table->string('country_code', 2)->default('DE');
            $table->string('billing_email')->nullable();
            $table->string('phone', 30)->nullable();
            // active | suspended (gesperrt, z. B. Zahlungsverzug)
            $table->string('status', 20)->default('active');
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
