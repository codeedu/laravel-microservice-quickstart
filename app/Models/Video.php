<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes, Traits\Uuid;

    const RATING_FREE = 'L';
    const RATING_LIST = [self::RATING_FREE, '10', '12', '14', '16', '18'];
    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
    ];
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}
