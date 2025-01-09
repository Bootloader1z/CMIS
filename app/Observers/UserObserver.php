<?php

namespace App\Observers;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->logHistory($user, 'CREATED','A new User has been created in User Management.!' , null, null, $user->toArray(), 'Created a new user.');
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $changes = $user->getChanges();

        foreach ($changes as $field => $newValue) {
            // Skip logging 'updated_at' and 'password' field changes
            if ($field === 'updated_at' || $field === 'password' || $field === 'isactive' || $field === 'remember_token') {
                continue;
            }

            $oldValue = $user->getOriginal($field);

            // Log history for other fields
            $this->logHistory($user, 'UPDATED', $field, $oldValue, $newValue, ['message' => 'A User details or information has been edited.!'], 'Updated user details.');
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $this->logHistory($user, 'DELETED', 'A User has been deleted in User Management.!', 'No DATA', 'No DATA', $user->toArray(), 'Deleted user record.');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(User $user, string $action, string $field = null, $oldValue = null, $newValue = null, array $details = null, string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = 'User Managed';
        $auditTrail->action = $action;
        $auditTrail->field = $field;
        $auditTrail->old_value = $oldValue;
        $auditTrail->new_value = $newValue;
        $auditTrail->details = $details ? json_encode($details) : null;
        $auditTrail->description = $description;
        $auditTrail->user_id = Auth::id(); // Assuming you have authenticated user
        $auditTrail->save();
    }
}
