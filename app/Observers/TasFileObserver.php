<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\TasFile;

class TasFileObserver
{
    /**
     * Handle the Admitted "created" event.
     */
    public function created(TasFile $TasFile)
    {
        // Log creation including details and added fields
        $this->logHistory($TasFile, 'created', null, null, null, $TasFile->toArray(), 'Created a New Department Record !');
    }

    /**
     * Handle the TasFile "updated" event.
     */
    public function updated(TasFile $TasFile)
    {
        $changes = $TasFile->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $TasFile->getOriginal($field);
    
            // Log history for other fields
            $this->logHistory($TasFile, 'updated', $field, $oldValue, $newValue, null, 'Updated a Department Field.');
        }
    }
    
    /**
     * Handle the Department "deleted" event.
     */
    public function deleted(TasFile $TasFile)
    {
        // Log deletion including details field
        $this->logHistory($TasFile, 'deleted', null, null, null, $TasFile->toArray(), 'Deleted a Department record');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(TasFile $TasFile, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = TasFile::class;
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