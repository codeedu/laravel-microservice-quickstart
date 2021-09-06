<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{
    private $castMember;

    protected function setup(): void
    {
        parent::setup();
        $this->castMember = new CastMember();
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'type'];
        $this->assertEquals($fillable, $this->castMember->getFillable());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $castMemberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits, $castMemberTraits);
    }

    public function testCastsAttribute()
    {
        $casts = ['id' => 'string', 'type' => 'integer'];
        $this->assertEquals($casts, $this->castMember->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->castMember->incrementing);
    }

    public function testDatesAttributes()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $this->assertEqualsCanonicalizing($dates, $this->castMember->getDates());
        $this->assertCount(count($dates), $this->castMember->getDates());
    }
}
