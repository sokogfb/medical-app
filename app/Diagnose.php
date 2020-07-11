<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnose extends Model
{
    /**
     * set global attributes
     */
    protected $fillable = [
        'symptom_id',
        'name',
        'accuracy',
        'diagnosis'
    ];

    /**
     * casts
     */
    protected $casts = [
        'diagnosis' => 'array'
    ];

    /**
     * symptom
     * @return BelongsTo
     */
    public function symptom()
    {
        return $this->belongsTo(Symptom::class);
    }
}
