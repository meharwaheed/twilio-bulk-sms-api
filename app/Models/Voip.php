<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voip extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'file', 'user_id'];
}
