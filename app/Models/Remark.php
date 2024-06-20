<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TasFile;
class admitted extends Model
{

    protected $table = 'admitted_files';

    protected $fillable = ['tas_file_id', 'remark'];

    public function logActivity($action, $changes = [])
{
    UserTimeline::create([
        'user_id' => Auth::id(), // Ensure Auth::id() returns the correct user ID
        'username' => Auth::user()->username,
        'action' => $action,
        'model_type' => self::class,
        'model_id' => $this->id,
        'changes' => json_encode($changes),
    ]);
}

public function logHistory($action, $changes)
{
    $history = $this->history ?? [];
    
    $history[] = [
        'action' => $action,
        'user_id' => Auth::id(), // Ensure Auth::id() returns the correct user ID
        'fullname' => Auth::user()->fullname,
        'username' => Auth::user()->username,
        'timestamp' => now()->toDateTimeString(),
        'changes' => $changes
    ];
    
    $this->update(['history' => $history]); // Update 'history' attribute
}

}
