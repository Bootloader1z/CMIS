<?php

namespace App\Events;

use App\Models\TasFile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TasFileUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tasFile;

    public function __construct(TasFile $tasFile)
    {
        $this->tasFile = $tasFile;
    }

    public function broadcastOn()
    {
        return channel('tas-file-events');
    }

    public function broadcastAs()
    {
        return 'tas-file.updated';
    }
}
