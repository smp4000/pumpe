<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Tenancy;

use App\Actions\CreateOrganization;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

/**
 * Anlage einer neuen Organization durch einen angemeldeten Benutzer
 * (Erst-Registrierung oder weiterer Betrieb). Delegiert an die
 * CreateOrganization-Action, die Rollen und erste Station mit anlegt.
 */
class RegisterOrganization extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('core.tenancy.register_organization');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('core.fields.organization_name'))
                ->helperText(__('core.fields.organization_name_help'))
                ->required()
                ->maxLength(255),

            TextInput::make('station_name')
                ->label(__('core.fields.first_station_name'))
                ->helperText(__('core.fields.first_station_name_help'))
                ->required()
                ->maxLength(255),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Organization
    {
        /** @var User $user */
        $user = auth()->user();

        return app(CreateOrganization::class)->execute(
            owner: $user,
            attributes: ['name' => $data['name']],
            stationName: $data['station_name'],
        );
    }
}
