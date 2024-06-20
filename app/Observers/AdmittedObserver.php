<?php

namespace App\Observers;

use App\Models\AuditTrail;
use App\Models\admitted;
use Illuminate\Support\Facades\Auth;

class AdmittedObserver
{
    /**
     * Handle the Admitted "created" event.
     */
    public function created(admitted $admitted)
    {
        // Log creation including details and added fields
        $this->logHistory($admitted, 'created', null, null, null, $admitted->toArray(), 'Created a New Case Admitted Record !');
    }

    /**
     * Handle the Admitted "updated" event.
     */
    public function updated(admitted $admitted)
    {
        $changes = $admitted->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $admitted->getOriginal($field);
    
            // Log history for other fields
            $this->logHistory($admitted, 'updated', $field, $oldValue, $newValue, null, 'Updated a Case Admitted Field.');
        }
    }
    
    /**
     * Handle the Admitted "deleted" event.
     */
    public function deleted(admitted $admitted)
    {
        // Log deletion including details field
        $this->logHistory($admitted, 'deleted', null, null, null, $admitted->toArray(), 'Deleted a Case Admitted record');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(admitted $admitted, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = admitted::class;
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
