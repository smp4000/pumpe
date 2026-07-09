<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Invitations\Pages;

use App\Actions\InviteMember;
use App\Filament\App\Resources\Invitations\InvitationResource;
use App\Models\Invitation;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data): Invitation {
                    /** @var User $inviter */
                    $inviter = auth()->user();

                    return app(InviteMember::class)->execute(
                        email: $data['email'],
                        role: $data['role'],
                        invitedBy: $inviter,
                    );
                })
                ->successNotificationTitle(__('core.invitations.invited')),
        ];
    }
}
