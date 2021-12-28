<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Genre;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;

class GenreUnitTest extends TestCase
{
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->genre = new Genre();
    }

    public function testFillable()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals($this->genre->getFillable(), $fillable);
    }

    public function testUseOfTrais()
    {
        $traits = [softDeletes::class, uuid::class];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEqualsCanonicalizing($traits, $genreTraits);
    }

    public function testCasts()
    {
        $casts = ['is_active' => 'boolean'];
        $this->assertEqualsCanonicalizing($casts, $this->genre->getCasts());
    }

    public function testDates()
    {
        $dates = ['created_at', 'deleted_at', 'updated_at'];
        $this->assertEqualsCanonicalizing($dates, $this->genre->getDates());
    }

    public function testNotIncreminting()
    {
        $this->assertFalse($this->genre->incrementing);
    }
}
