<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'phone',
        'is_active',
        'status'
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var string[]
     */
    protected $appends = ['updated_at_formatted'];


    /**
     * The attributes that should be cast.
     *
     * @var string[]
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function updatedAtFormatted(): Attribute
    {
        return new Attribute(
            get: fn() => date('d/m/y h:i A', strtotime($this->updated_at))
        );
    }

    /**
     * Get the campaign that owns the phone number.
     *
     * @return BelongsTo
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
