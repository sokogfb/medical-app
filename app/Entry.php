<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use Uuids, SoftDeletes;

    /**
     * stop auto increment
     */
    public $incrementing = false;

    /**
     * set global attributes
     */
    protected $fillable = [
        'user_id',
        'entryNumber',
    ];

    /**
     * user
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * symptoms
     * @return HasMany
     */
    public function symptom(): HasMany
    {
        return $this->hasMany(Symptom::class);
    }

    /**
     * diagnose
     * @return HasMany
     */
    public function diagnose(): HasMany
    {
        return $this->hasMany(Diagnose::class);
    }
}
