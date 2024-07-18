<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'website',
        'is_deleted'
    ];

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }
}
