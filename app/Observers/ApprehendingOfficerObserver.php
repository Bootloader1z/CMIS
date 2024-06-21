<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\ApprehendingOfficer;

class ApprehendingOfficerObserver
{
   /**
     * Handle the Admitted "created" event.
     */
    public function created(ApprehendingOfficer $ApprehendingOfficer)
    {
        // Log creation including details and added fields
        $this->logHistory($ApprehendingOfficer, 'CREATED', 'No DATA', 'No DATA', 'No DATA', $ApprehendingOfficer->toArray(), 'Created a New Case ApprehendingOfficer Record !');
    }

    /**
     * Handle the ApprehendingOfficer "updated" event.
     */
    public function updated(ApprehendingOfficer $ApprehendingOfficer)
    {
        $changes = $ApprehendingOfficer->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $ApprehendingOfficer->getOriginal($field);
    
            // Log history for other fields
            $this->logHistory($ApprehendingOfficer, 'UPDATED', $field, $oldValue, $newValue, 'No DATA', 'Updated a Case ApprehendingOfficer Field.');
        }
    }
    
    /**
     * Handle the ApprehendingOfficer "deleted" event.
     */
    public function deleted(ApprehendingOfficer $ApprehendingOfficer)
    {
        // Log deletion including details field
        $this->logHistory($ApprehendingOfficer, 'DELETED', 'No DATA', 'No DATA', 'No DATA', $ApprehendingOfficer->toArray(), 'Deleted a Case ApprehendingOfficer record');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(ApprehendingOfficer $ApprehendingOfficer, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = 'Apprehending Officer Managed';
        $auditTrail->action = $action;
        $auditTrail->field = $field;
        $auditTrail->old_value = $oldValue;
        $auditTrail->new_value = $newValue;
        $auditTrail->details = $details ? json_encode($details) : null;
        $auditTrail->description = $description;
        $auditTrail->user_id = Auth::id();
        $auditTrail->save();
    }
}
