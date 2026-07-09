<?php

declare(strict_types=1);

namespace Modules\Playground\Policies;

use App\Models\User;
use Modules\Playground\Models\Note;

/**
 * Dritte Ebene der Lizenz-/Rechteprüfung: Berechtigungen des Moduls
 * (siehe module.json) — die Tenant-Trennung übernimmt der TenantScope.
 */
class NotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('playground.notes.view');
    }

    public function view(User $user, Note $note): bool
    {
        return $user->can('playground.notes.view');
    }

    public function create(User $user): bool
    {
        return $user->can('playground.notes.create');
    }

    public function update(User $user, Note $note): bool
    {
        return $user->can('playground.notes.update');
    }

    public function delete(User $user, Note $note): bool
    {
        return $user->can('playground.notes.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('playground.notes.delete');
    }
}
