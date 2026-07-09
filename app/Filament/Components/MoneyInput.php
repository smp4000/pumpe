<?php

declare(strict_types=1);

namespace App\Filament\Components;

use Filament\Forms\Components\TextInput;

/**
 * Geld-Eingabefeld: Anzeige und Eingabe in Euro, Speicherung als
 * Integer-Cents (ADR-0006). Für alle Geldbeträge in Formularen verwenden —
 * niemals Float-Spalten oder eigene Umrechnungen in Resources.
 */
final class MoneyInput
{
    public static function make(string $name): TextInput
    {
        return TextInput::make($name)
            ->numeric()
            ->step(0.01)
            ->suffix('€')
            ->default(0)
            // Cents (DB) → Euro (Anzeige)
            ->formatStateUsing(
                fn (int|string|null $state): string => number_format(((int) ($state ?? 0)) / 100, 2, '.', ''),
            )
            // Euro (Eingabe) → Cents (DB); Komma als Dezimaltrenner zulassen
            ->dehydrateStateUsing(function (string|float|int|null $state): int {
                if ($state === null || $state === '') {
                    return 0;
                }

                $normalized = (float) str_replace(',', '.', (string) $state);

                return (int) round($normalized * 100);
            });
    }
}
