<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\G5ChatMessage;

class G5ChatMessageObserver
{
      /**
     * Handle the G5ChatMessage "created" event.
     */
    public function created(G5ChatMessage $G5ChatMessage)
    {
        // Log creation including details and added fields
        $this->logHistory($G5ChatMessage, 'created', null, null, null, $G5ChatMessage->toArray(), 'Created a New Chat !');
    }

    /**
     * Handle the G5ChatMessage "updated" event.
     */
    public function updated(G5ChatMessage $G5ChatMessage)
    {
        $changes = $G5ChatMessage->getChanges();
    
        foreach ($changes as $field => $newValue) {
            // Skip logging 'history' and 'updated_at' field changes
            if ($field === 'history' || $field === 'updated_at') {
                continue;
            }
    
            $oldValue = $G5ChatMessage->getOriginal($field);
    
            // Log history for other fields
            $this->logHistory($G5ChatMessage, 'updated', $field, $oldValue, $newValue, null, 'Edited A Chat.!');
        }
    }
    
    /**
     * Handle the G5ChatMessage "deleted" event.
     */
    public function deleted(G5ChatMessage $G5ChatMessage)
    {
        // Log deletion including details field
        $this->logHistory($G5ChatMessage, 'deleted', null, null, null, $G5ChatMessage->toArray(), 'Deleted a Chat');
    }

    /**
     * Log the changes to the Audit Trail.
     */
    protected function logHistory(G5ChatMessage $G5ChatMessage, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?array $details = null, ?string $description = null)
    {
        $auditTrail = new AuditTrail();
        $auditTrail->model = G5ChatMessage::class;
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
