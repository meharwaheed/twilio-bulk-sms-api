<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkSms extends Model
{
    use HasFactory;

    protected $fillable = [
        'blast_name',
        'from_number',
        'message',
        'is_schedule',
        'schedule_date',
        'status',
        'csv_file',
    ];
}
