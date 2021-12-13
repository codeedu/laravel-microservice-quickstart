<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\CastMember;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;

class CastMemberTest extends TestCase
{
    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = new CastMember();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->castMember = new CastMember();
    }

    public function testFillable()
    {
        $fillable = ['name', 'type', 'is_active'];
        $this->assertEquals($this->castMember->getFillable(), $fillable);
    }

    public function testUseOfTrais()
    {
        $traits = [softDeletes::class, uuid::class];
        $castMemberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEqualsCanonicalizing($traits, $castMemberTraits);
    }

    public function testCasts()
    {
        $casts = ['is_active' => 'boolean'];
        $this->assertEqualsCanonicalizing($casts, $this->castMember->getCasts());
    }

    public function testDates()
    {
        $dates = ['created_at', 'deleted_at', 'updated_at'];
        $this->assertEqualsCanonicalizing($dates, $this->castMember->getDates());
    }

    public function testNotIncreminting()
    {
        $this->assertFalse($this->castMember->incrementing);
    }
}
