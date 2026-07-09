<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registry aller Module. Quelle der Wahrheit sind die module.json-
     * Manifeste unter app-modules/ — diese Tabelle wird per
     * `php artisan modules:sync` abgeglichen und dient als FK-Ziel
     * für Lizenzen.
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            // Stabiler Identifier aus dem Manifest (kebab-case), wird nie geändert
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            // Core-Module sind ohne Lizenz für alle Organizations aktiv
            $table->boolean('is_core')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
