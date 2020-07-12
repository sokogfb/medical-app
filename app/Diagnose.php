<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnose extends Model
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
     * entry
     * @return BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }
}
