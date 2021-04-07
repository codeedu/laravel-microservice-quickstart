<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;
use Tests\TestCase;

class CastMemberTest extends TestCase
{
    private $castMember;

    public static function setUpBeforeClass(): void
    {
        // parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = new CastMember();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // 
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

    public function testFillableAttribute()
    {
        $fillable = ['name', 'type'];
        $this->assertEquals($fillable, $this->castMember->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->castMember->getDates());
        };
        $this->assertCount(count($dates), $this->castMember->getDates());
    }

    // public function testCasts()
    // {
    //     $casts = ['id' => 'string', 'is_active' => 'boolean'];
    //     $this->assertEquals($casts, $this->castMember->getCasts());
    // }

    public function testIncrementing()
    {
        $this->assertFalse($this->castMember->incrementing);
    }
}
