<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // Comienzan las relaciones del modelo User

    /*  public function dependencies(){
          return $this->belongsToMany();
      }*/

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class,'monitor_id','id');
    }

    public function dependencies()
    {
        return $this->belongsToMany(Dependency::class)->withPivot('role');
    }

    public function getSupervisedDepencyId()
    {
        $supervisorRoleId = 1;

        $dependency_user = DB::table('dependency_user')
            ->where('user_id', $this->id)
            ->where('role', $supervisorRoleId)
            ->first();

        if ($dependency_user === null) {
            return null;
        }

        return $dependency_user->dependency_id;

    }


}
