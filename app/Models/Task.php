<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'title', 'description', 'due_date', 'priority_id',
        'status_id', 'task_type_id', 'category_id', 'user_assignments'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_assignments');
    }

    

}
