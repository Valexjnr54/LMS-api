<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecturer_id',
        'course_id',
        'course_name',
        'course_code',
    ];

}
