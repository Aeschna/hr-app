<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['type', 'model_type', 'model_id'];

    // Optionally, you can set the name of the 'deleted_at' column if it's different
    protected $dates = ['deleted_at'];
}

