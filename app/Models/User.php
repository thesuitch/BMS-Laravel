<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    // use HasApiTokens, HasFactory, Notifiable;
    use  Notifiable;

    protected $primaryKey = 'row_id';  // Ensure this line is present


    protected $table = 'log_info';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     
   

     
    // protected $fillable = [
    //     // 'name',
    //     'email',
    //     'password',
    //     'is_admin',
    //     'user_type'
    //     // 'login_datetime'
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    //     'password' => 'string',
    // ];


    public function getJWTIdentifier()
    {         
        return $this->getKey();     
    }
    public function getJWTCustomClaims()
    {         
        return [];
    } 

    public function  userinfo()
    {
        return $this->belongsTo(UserInfo::class,'user_id','id');
    }
}
