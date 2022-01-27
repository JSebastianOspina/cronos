<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
 * @mixin \Eloquent
 */
class Dependency extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function getMonitors()
    {
        return DB::table('dependency_user')
            ->select([
                'users.id', 'users.name', 'users.email'
            ])
            ->where('dependency_id', '=', $this->id)
            ->where('dependency_user.role', '=', 0)
            ->join('users', 'users.id', '=', 'dependency_user.user_id')
            ->get();
    }
}
