<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'origin_id',
        'destination_id',
        'name',
        'description',
        'schedule_expression',
        'next_run_at',
        'last_run_at',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the origin that owns the task.
     */
    public function origin()
    {
        return $this->belongsTo(Origin::class);
    }

    /**
     * Get the destination that owns the task.
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
