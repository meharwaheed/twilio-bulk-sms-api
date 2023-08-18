<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Voip extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'file', 'user_id'];

    protected $appends = ['file_path'];

    public function filePath(): Attribute
    {
        return new Attribute(
            get: fn() => $this->file ? asset('storage') . '/' . $this->file : null
//            get: fn() => $this->file ? env('AWS_CDN') . '/' . $this->file : null
        );
    }
}
