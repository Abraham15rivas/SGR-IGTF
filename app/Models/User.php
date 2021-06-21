<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'role_id',
        'session_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Métodos de AdminLTE
    public function adminlte_image() {
        $profile = Profile::where('user_id', $this->id)->first();
        $value   = '/images/default.png';

        if(!empty($profile)) {
            if($profile->image) {
                $value = "/storage/$profile->image";
            }
        }
        return $value;
    }

    public function adminlte_desc() {
        return $this->role->name;
    }

    public function adminlte_profile_url() {
        return 'profile/index';
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function profile() {
        return $this->hasOne(Profile::class);
    }

    public function logs() {
        return $this->hasMany(Log::class);
    }

    public function notification() {
        return $this->hasOne(Notification::class);
    }
}
