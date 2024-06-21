<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\TasFile;

class TasFileObserver
{
    /**
     * Handle the TasFile "created" event.
     */
    public function created(TasFile $tasFile)
    {
        // Log creation including details and added fields
        $this->logHistory($tasFile, 'CREATED', null, null, null, $tasFile->toArray(), 'Created a New Case Contested Record!');
    }

    /**
     * Handle the TasFile "updated" event.
     */
    public function updated(TasFile $tasFile)
    {
        $changes = $tasFile->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $tasFile->getOriginal($field);
    
            // Log history for other fields
            $this->logHistory($tasFile, 'UPDATED', $field, $oldValue, $newValue, null, 'Updated a Case Contested Field.');
        }
    }
    
    /**
     * Handle the TasFile "deleted" event.
     */
    public function deleted(TasFile $tasFile)
    {
        // Log deletion including details field
        $this->logHistory($tasFile, 'DELETED', null, null, null, $tasFile->toArray(), 'Deleted a Case Contested record');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(TasFile $tasFile, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = 'Case Contested';

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
