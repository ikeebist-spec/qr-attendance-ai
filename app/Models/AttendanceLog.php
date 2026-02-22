<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AttendanceLog extends Model
{
    protected $fillable = ['student_id', 'event_id', 'student_name', 'section', 'scanned_at'];
}
