<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Config
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Config newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config query()
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereValue($value)
 */
	class Config extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Dependency
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereUpdatedAt($value)
 */
	class Dependency extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GoogleCalendar
 *
 * @property int $id
 * @property string $google_calendar_id
 * @property string $url
 * @property int $user_id
 * @property int $dependency_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\GoogleCalendarFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar whereDependencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar whereGoogleCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCalendar whereUserId($value)
 */
	class GoogleCalendar extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Record extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Schedule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Record[] $records
 * @property-read int|null $records_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Schedule[] $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

