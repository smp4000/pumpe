<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Schichtabrechnungen mit Soll/Ist-Positionen je Zahlart.
     * Geldbeträge als Integer-Cents (ADR-0006); freigegebene
     * Abrechnungen sind unveränderlich (GoBD, Storno statt Änderung).
     */
    public function up(): void
    {
        Schema::create('shift_reconciliation_shifts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('station_id')->constrained()->cascadeOnDelete();
            // Schichtverantwortlicher (Personalstammsatz)
            $table->foreignUlid('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            // open | submitted | approved | cancelled
            $table->string('status', 20)->default('open');
            // Aggregierte Summen der Positionen (werden bei jeder Änderung
            // neu berechnet und beim Abschluss eingefroren)
            $table->bigInteger('expected_total_cents')->default(0);
            $table->bigInteger('counted_total_cents')->default(0);
            $table->bigInteger('difference_cents')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignUlid('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUlid('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();

            // Expliziter kurzer Name — der generierte überschreitet MySQLs 64-Zeichen-Limit
            $table->index(['organization_id', 'station_id', 'starts_at'], 'sr_shifts_org_station_starts_idx');
        });

        Schema::create('shift_reconciliation_entries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('shift_id')->constrained('shift_reconciliation_shifts')->cascadeOnDelete();
            // cash | debit_card | credit_card | voucher | other
            $table->string('payment_method', 20);
            // Soll laut Kassensystem
            $table->bigInteger('expected_amount_cents')->default(0);
            // Ist laut Zählung
            $table->bigInteger('counted_amount_cents')->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_reconciliation_entries');
        Schema::dropIfExists('shift_reconciliation_shifts');
    }
};
