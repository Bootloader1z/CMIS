<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\TrafficViolation;

class TrafficViolationObserver
{
   /**
     * Handle the Admitted "created" event.
     */
    public function created(TrafficViolation $TrafficViolation)
    {
        // Log creation including details and added fields
        $this->logHistory($TrafficViolation, 'CREATED', 'No DATA', 'No DATA', 'No DATA', $TrafficViolation->toArray(), 'Created a New TrafficViolation Record !');
    }

    /**
     * Handle the TrafficViolation "updated" event.
     */
    public function updated(TrafficViolation $TrafficViolation)
    {
        $changes = $TrafficViolation->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $TrafficViolation->getOriginal($field);
    
            // Log history for other fields
           // $this->logHistory($TrafficViolation, 'UPDATED', $field, $oldValue, $newValue, 'No DATA', 'Updated a TrafficViolation Field.');
        }
    }
    
    /**
     * Handle the Department "TrafficViolation" event.
     */
    public function TrafficViolation(TrafficViolation $TrafficViolation)
    {
        // Log deletion including details field
        $this->logHistory($TrafficViolation, 'DELETED', 'No DATA', 'No DATA', 'No DATA', $TrafficViolation->toArray(), 'TrafficViolation a Department record');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(TrafficViolation $TrafficViolation, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = 'Traffic Violation';

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
