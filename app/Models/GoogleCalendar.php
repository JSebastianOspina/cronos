<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @mixin \Eloquent
 */
class GoogleCalendar extends Model
{
    use HasFactory;
}
