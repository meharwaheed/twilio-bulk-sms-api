<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'blast_name',
        'from_number',
        'message',
        'is_schedule',
        'schedule_date',
        'timezone',
        'status',
        'csv_file',
        'user_id',
        'converted_date'
    ];

    protected $appends = ['csv_file_path'];

    public function csvFilePath(): Attribute
    {
        return new Attribute(
            get: fn() => $this->csv_file ? asset('storage') . '/' . $this->csv_file : null
        );
    }

    /**
     * Get the numbers for the campaign
     *
     * @return HasMany
     */
    public function campaignNumbers(): HasMany
    {
        return $this->hasMany(CampaignNumber::class);
    }


    /**
     * The attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'is_schedule' => 'boolean',
        'schedule_date' => 'datetime',
    ];
}
