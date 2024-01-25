<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnroll extends Model
{
    use HasFactory;

    protected $fillable = ['student_id','semester','course_id', 'course_code','course_title','credit_load'];
}
