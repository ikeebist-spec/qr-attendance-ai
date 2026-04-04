<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Event extends Model
{
    protected $fillable = [
        'name', 'date', 'month', 'type', 'fine', 'is_single_scan', 'start_time', 'end_time',
        'morn_in_start', 'morn_in_end', 'morn_out_start', 'morn_out_end',
        'aft_in_start', 'aft_in_end', 'aft_out_start', 'aft_out_end'
    ];
}
