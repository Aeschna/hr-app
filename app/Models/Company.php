<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'website',
        'user_id',
    ];
    protected $dates = ['deleted_at'];
   
    public function users()
    {
        return $this->hasMany(User::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($company) {
            ActivityLog::create([
                'type' => 'Added',
                'model_type' => 'Company',
                'model_id' => $company->id,
            ]);
        });

        static::deleted(function ($company) {
            ActivityLog::create([
                'type' => 'Deleted',
                'model_type' => 'Company',
                'model_id' => $company->id,
            ]);
        });

        static::restored(function ($company) {
            ActivityLog::create([
                'type' => 'Restored',
                'model_type' => 'Company',
                'model_id' => $company->id,
            ]);
        });
    }
}

