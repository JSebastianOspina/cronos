<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
