<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Einladungs-E-Mail mit Annahme-Link. Bewusst nicht queued, solange lokal
 * kein Queue-Worker läuft — wird mit der Queue-Infrastruktur umgestellt.
 */
class InvitationNotification extends Notification
{
    public function __construct(private readonly Invitation $invitation) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Explizit laden — Lazy Loading ist außerhalb der Produktion verboten
        $this->invitation->loadMissing('organization');

        $organizationName = $this->invitation->organization->name;

        return (new MailMessage)
            ->subject(__('core.invitations.mail_subject', ['organization' => $organizationName]))
            ->greeting(__('core.invitations.mail_greeting'))
            ->line(__('core.invitations.mail_intro', ['organization' => $organizationName]))
            ->action(
                __('core.invitations.mail_button'),
                route('invitations.show', ['token' => $this->invitation->token]),
            )
            ->line(__('core.invitations.mail_expiry', [
                'date' => $this->invitation->expires_at->timezone('Europe/Berlin')->format('d.m.Y H:i'),
            ]))
            ->salutation(__('core.invitations.mail_salutation'));
    }
}
