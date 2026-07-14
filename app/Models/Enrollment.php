<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'registrations';

    protected $fillable = ['user_id', 'course_id', 'status', 'completion_percentage', 'completion_synced_at'];

    protected $casts = [
        'completion_synced_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
