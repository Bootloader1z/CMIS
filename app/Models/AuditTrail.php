<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = ['model', 'added', 'details','field', 'old_value', 'new_value', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
