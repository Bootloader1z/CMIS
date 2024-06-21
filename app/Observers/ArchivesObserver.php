<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\Archives;

class ArchivesObserver
{
    /**
     * Handle the Admitted "created" event.
     */
    public function created(Archives $Archives)
    {
        // Log creation including details and added fields
        $this->logHistory($Archives, 'CREATED', 'No DATA', 'No DATA', 'No DATA', $Archives->toArray(), 'Created a New Archives Record !');
    }

    /**
     * Handle the Archives "updated" event.
     */
    public function updated(Archives $Archives)
    {
        $changes = $Archives->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $Archives->getOriginal($field);
    
            // Log history for other fields
            $this->logHistory($Archives, 'UPDATED', $field, $oldValue, $newValue, 'No DATA', 'Updated a Archives Field.');
        }
    }
    
    /**
     * Handle the Archives "deleted" event.
     */
    public function deleted(Archives $Archives)
    {
        // Log deletion including details field
        $this->logHistory($Archives, 'DELETED', 'No DATA', 'No DATA', 'No DATA', $Archives->toArray(), 'Deleted a Archives record');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(Archives $Archives, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = 'Case Archives Managed';
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
