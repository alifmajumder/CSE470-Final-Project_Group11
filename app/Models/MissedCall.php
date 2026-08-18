<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MissedCall — records every incoming missed call to the system number.
 *
 * @property int         $id
 * @property string      $caller_number   E.164 phone of the worker who called
 * @property string|null $called_number   Our system's DID / virtual number
 * @property int|null    $worker_id       Resolved user id (null if unregistered)
 * @property string|null $district        Worker's district at time of call
 * @property int         $jobs_sent       How many job SMS were dispatched
 * @property string      $status          processing|jobs_sent|no_jobs|welcome_sent|error
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MissedCall extends Model
{
    protected $fillable = [
        'caller_number',
        'called_number',
        'worker_id',
        'district',
        'jobs_sent',
        'status',
    ];

    protected $casts = [
        'jobs_sent' => 'integer',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
