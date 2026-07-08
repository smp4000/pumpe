<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * organization_user: Mitgliedschaft eines Users in einer Organization
     * (n:m — z. B. Steuerberater mit Zugang zu mehreren Mandanten).
     *
     * station_user: optionale Einschränkung auf bestimmte Stationen.
     * Keine Einträge = Zugriff auf alle Stationen der Organization.
     */
    public function up(): void
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['organization_id', 'user_id']);
        });

        Schema::create('station_user', function (Blueprint $table) {
            $table->foreignUlid('station_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['station_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_user');
        Schema::dropIfExists('organization_user');
    }
};
