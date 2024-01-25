<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedGroupCourse extends Model
{
    use HasFactory;

    protected $fillable = ['course_group_code','course_id', 'credit_load','course_title','course_code'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
