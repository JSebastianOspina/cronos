<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Record
 *
 * @property int $id
 * @property int $supervisor_id
 * @property int $monitor_id
 * @property int $dependency_id
 * @property string $start_planned_date
 * @property string $end_planned_date
 * @property string|null $start_monitor_date
 * @property string|null $end_monitor_date
 * @property string|null $start_approved_date
 * @property string|null $end_approved_date
 * @property string|null $total_planned_minutes
 * @property string|null $total_monitor_minutes
 * @property string|null $total_approved_minutes
 * @property string|null $status
 * @property mixed|null $observation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $monitor
 * @property-read \App\Models\User $supervisor
 * @method static \Illuminate\Database\Eloquent\Builder|Record newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record query()
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereDependencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereEndApprovedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereEndMonitorDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereEndPlannedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereObservation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereStartApprovedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereStartMonitorDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereStartPlannedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereTotalApprovedMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereTotalMonitorMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereTotalPlannedMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Record extends Model
{
    use HasFactory;


    public function monitor()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class);
    }


}
