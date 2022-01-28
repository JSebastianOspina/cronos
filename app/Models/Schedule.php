<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property string $start_hour
 * @property string $end_hour
 * @property string $type
 * @property string|null $date
 * @property int|null $day_of_week
 * @property string|null $google_event_id
 * @property int $monitor_id
 * @property int $supervisor_id
 * @property int $dependency_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $monitor
 * @property-read \App\Models\User $supervisor
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDependencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereEndHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereGoogleEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereStartHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Schedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function monitor()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class);
    }



}
