<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id', // Added company_id to mass assignable attributes
        'api_token',
       
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
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
       // 'password' => 'hashed',
    ];
    public function isAdmin()
{
    return $this->is_admin; 
}
public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            ActivityLog::create([
                'type' => 'Added',
                'model_type' => 'User',
                'model_id' => $user->id,
            ]);
        });

        static::deleted(function ($user) {
            ActivityLog::create([
                'type' => 'Deleted',
                'model_type' => 'User',
                'model_id' => $user->id,
            ]);
        });

        static::restored(function ($user) {
            ActivityLog::create([
                'type' => 'Restored',
                'model_type' => 'User',
                'model_id' => $user->id,
            ]);
        });
}
}
