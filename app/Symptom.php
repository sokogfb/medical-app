<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Symptom extends Model
{
    /**
     * set global attributes
     */
    protected $fillable = [
        'user_id',
        'symptoms'
    ];

    /**
     * casts
     */
    protected $casts = [
        'symptoms' => 'array'
    ];

    /**
     * user
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * diagnoses
     * @return HasMany
     */
    public function diagnose()
    {
        return $this->hasMany(Diagnose::class);
    }
}
