<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes, Uuid;
    protected $fillable = ['name','is_active'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id'    => 'string'
    ];

    public $incrementing = false;
}
