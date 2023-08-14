<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptOut extends Model
{
    use HasFactory;

    protected $fillable = ['in_keywords', 'out_keywords'];
}
