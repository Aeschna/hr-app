<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_id',
    ];
    protected $dates = ['deleted_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($employee) {
            ActivityLog::create([
                'type' => 'Added',
                'model_type' => 'Employee',
                'model_id' => $employee->id,
            ]);
        });

        static::deleted(function ($employee) {
            ActivityLog::create([
                'type' => 'Deleted',
                'model_type' => 'Employee',
                'model_id' => $employee->id,
            ]);
        });

        static::restored(function ($employee) {
            ActivityLog::create([
                'type' => 'Restored',
                'model_type' => 'Employee',
                'model_id' => $employee->id,
            ]);
        });
}
}