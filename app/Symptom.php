<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Symptom extends Model
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
        'entry_id',
        'symptomID',
        'symptomName',
        'is_processed',
    ];

    /**
     * entry
     * @return BelongsTo
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class,'entry_id');
    }
}
