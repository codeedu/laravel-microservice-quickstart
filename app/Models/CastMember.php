<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes, Traits\Uuid;
    protected $fillable = ['name', 'type', 'is_active'];
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = ['is_active' => 'boolean'];
}
