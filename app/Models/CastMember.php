<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    const TYPE_DYRECTOR = 1;
    const TYPE_MEMBER = 2;

    use SoftDeletes, Traits\Uuid;
    protected $fillable = ['name', 'type', 'is_active'];
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = ['is_active' => 'boolean'];

    public static function getPossibleMembers(): array
    {
        return [
            self::TYPE_MEMBER,
            self::TYPE_DYRECTOR,
        ];
    }
}
