<?php

namespace App\Listeners;

use App\Models\Interaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class AdmittedActivityListener
{
    /**
     * Handle the admitted created event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function created($event)
    {
        $this->logActivity('created', $event->admitted);
    }

    /**
     * Handle the admitted updated event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function updated($event)
    {
        $this->logActivity('updated', $event->admitted);
    }

    /**
     * Handle the admitted deleted event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function deleted($event)
    {
        $this->logActivity('deleted', $event->admitted);
    }

    /**
     * Log activity for the admitted model.
     *
     * @param string $action
     * @param \App\Models\Admitted $admitted
     * @return void
     */
    protected function logActivity($action, $admitted)
    {
        // Retrieve current user's information
        $user = Auth::user();

        // Create a new interaction record
        Interaction::create([
            'admitted_id' => $admitted->id,
            'action' => $action,
            'user_id' => $user->id,
            'fullname' => $user->fullname,
            'username' => $user->username,
            'timestamp' => now()->toDateTimeString(),
            'changes' => $admitted->getChanges()
        ]);
    }
}
