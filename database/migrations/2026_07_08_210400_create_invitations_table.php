<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Einladungen neuer Benutzer in eine Organization. Der Empfänger erhält
     * einen signierten Link; beim Annehmen wird das User-Konto erstellt bzw.
     * verknüpft und die hinterlegte Rolle zugewiesen.
     */
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            // Name der Rolle (tenant-spezifisch, spatie/laravel-permission)
            $table->string('role');
            // Optional: direkt mit einem Personalstammsatz verknüpfen
            $table->foreignUlid('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
