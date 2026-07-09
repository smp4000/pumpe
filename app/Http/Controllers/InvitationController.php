<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AcceptInvitation;
use App\Http\Requests\AcceptInvitationRequest;
use App\Models\Invitation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Öffentliche Annahme einer Einladung. Der Token-Link kommt aus der
 * Einladungs-E-Mail; der Zugriff erfolgt ohne Tenant-Kontext (Gast),
 * daher withoutTenancy() bei der Token-Suche.
 */
class InvitationController extends Controller
{
    public function show(string $token, AcceptInvitation $acceptInvitation): View|RedirectResponse
    {
        $invitation = $this->findByToken($token);

        if (! $invitation->isPending()) {
            return view('invitations.expired');
        }

        $existingUser = User::query()->where('email', $invitation->email)->first();

        // Angemeldeter Benutzer mit passender Adresse: direkt annehmen
        if (Auth::check()) {
            /** @var User $current */
            $current = Auth::user();

            if (mb_strtolower($current->email) === $invitation->email) {
                $acceptInvitation->execute($invitation, $current);

                return redirect()->to($this->panelUrl($invitation));
            }

            return view('invitations.mismatch', [
                'invitation' => $invitation,
                'current' => $current,
            ]);
        }

        // Konto existiert bereits: erst anmelden, danach führt der Link zum Ziel
        if ($existingUser !== null) {
            session()->put('url.intended', route('invitations.show', ['token' => $token]));

            return redirect()->to(Filament::getPanel('app')->getLoginUrl() ?? '/app/login');
        }

        // Neues Konto anlegen
        return view('invitations.accept', ['invitation' => $invitation]);
    }

    public function store(
        string $token,
        AcceptInvitationRequest $request,
        AcceptInvitation $acceptInvitation,
    ): RedirectResponse {
        $invitation = $this->findByToken($token);

        abort_unless($invitation->isPending(), 410);

        // Falls das Konto inzwischen existiert, keine Doppelanlage
        abort_if(User::query()->where('email', $invitation->email)->exists(), 409);

        $user = new User([
            'name' => $request->validated('name'),
            'email' => $invitation->email,
            'password' => $request->validated('password'),
        ]);

        // Die Adresse gilt durch den zugestellten Einladungslink als bestätigt
        $user->email_verified_at = now();
        $user->save();

        $acceptInvitation->execute($invitation, $user);

        Auth::login($user);

        return redirect()->to($this->panelUrl($invitation));
    }

    private function findByToken(string $token): Invitation
    {
        return Invitation::withoutTenancy()
            ->with('organization')
            ->where('token', $token)
            ->firstOrFail();
    }

    private function panelUrl(Invitation $invitation): string
    {
        return Filament::getPanel('app')->getUrl($invitation->organization);
    }
}
