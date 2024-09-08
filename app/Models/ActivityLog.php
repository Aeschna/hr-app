<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property string                          $type
 * @property string                          $model_type
 * @property int                             $model_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ActivityLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['type', 'model_type', 'model_id'];

    // Optionally, you can set the name of the 'deleted_at' column if it's different
    protected $dates = ['deleted_at'];
}
