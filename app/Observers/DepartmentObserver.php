<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\Department;

class DepartmentObserver
{
    /**
     * Handle the Admitted "created" event.
     */
    public function created(Department $Department)
    {
        // Log creation including details and added fields
        $this->logHistory($Department, 'CREATED', 'No DATA', 'No DATA', 'No DATA', $Department->toArray(), 'Created a New Department Record !');
    }

    /**
     * Handle the Department "updated" event.
     */
    public function updated(Department $Department)
    {
        $changes = $Department->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $Department->getOriginal($field);
    
            // Log history for other fields
            $this->logHistory($Department, 'UPDATED', $field, $oldValue, $newValue, 'No DATA', 'Updated a Department Field.');
        }
    }
    
    /**
     * Handle the Department "deleted" event.
     */
    public function deleted(Department $Department)
    {
        // Log deletion including details field
        $this->logHistory($Department, 'DELETED', 'No DATA', 'No DATA', 'No DATA', $Department->toArray(), 'Deleted a Department record');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(Department $Department, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = 'Department';
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
