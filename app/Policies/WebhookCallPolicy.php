<?php

namespace App\Policies;

use App\Models\User;
use Spatie\WebhookClient\Models\WebhookCall;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookCallPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_webhook::call');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WebhookCall $webhookCall): bool
    {
        return $user->can('view_webhook::call');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_webhook::call');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WebhookCall $webhookCall): bool
    {
        return $user->can('update_webhook::call');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WebhookCall $webhookCall): bool
    {
        return $user->can('delete_webhook::call');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_webhook::call');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, WebhookCall $webhookCall): bool
    {
        return $user->can('force_delete_webhook::call');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_webhook::call');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, WebhookCall $webhookCall): bool
    {
        return $user->can('restore_webhook::call');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_webhook::call');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, WebhookCall $webhookCall): bool
    {
        return $user->can('replicate_webhook::call');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_webhook::call');
    }
}
