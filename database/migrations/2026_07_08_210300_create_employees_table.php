<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Employees sind Personalstammsätze — getrennt vom Login-Konto (users),
     * siehe ADR-0005. user_id ist optional: nicht jeder Mitarbeiter hat
     * einen Systemzugang.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            // Stammstation des Mitarbeiters; Einsatz an anderen Stationen bleibt möglich
            $table->foreignUlid('station_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('personnel_number', 50)->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->date('hired_at')->nullable();
            $table->date('terminated_at')->nullable();
            // active | inactive (z. B. ruhendes Arbeitsverhältnis) | terminated
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'personnel_number']);
            // Ein Login-Konto kann pro Organization nur einem Mitarbeiter gehören
            $table->unique(['organization_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
