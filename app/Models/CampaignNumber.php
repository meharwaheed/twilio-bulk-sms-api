<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignNumber extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id', 'phone'];

    protected $appends = ['created_at_formatted'];

    public function createdAtFormatted(): Attribute
    {
        return new Attribute(
            get: fn() => date('d/m/y h:i A', strtotime($this->created_at))
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
