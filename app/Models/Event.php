<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Event extends Model
{
    protected $fillable = ['name', 'date', 'month', 'type', 'fine', 'start_time', 'end_time'];
}
