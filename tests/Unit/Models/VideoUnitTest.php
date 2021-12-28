<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Video;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;

class VideoUnitTest extends TestCase
{
    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->video = new Video();
    }

    public function testFillable()
    {
        $fillable = [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',    
        ];
        $this->assertEquals($this->video->getFillable(), $fillable);
    }

    public function testUseOfTrais()
    {
        $traits = [softDeletes::class, uuid::class];
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEqualsCanonicalizing($traits, $videoTraits);
    }

    public function testCasts()
    {
        $casts = [
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer',    
        ];
        $this->assertEqualsCanonicalizing($casts, $this->video->getCasts());
    }

    public function testDates()
    {
        $dates = ['created_at', 'deleted_at', 'updated_at'];
        $this->assertEqualsCanonicalizing($dates, $this->video->getDates());
    }

    public function testNotIncreminting()
    {
        $this->assertFalse($this->video->incrementing);
    }
}
