<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;

class CategoryTest extends TestCase
{
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->category = new Category();
    }

    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($this->category->getFillable(), $fillable);
    }

    public function testUseOfTrais()
    {
        $traits = [softDeletes::class, uuid::class];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEqualsCanonicalizing($traits, $categoryTraits);
    }

    public function testCasts()
    {
        $casts = ['is_active' => 'boolean'];
        $this->assertEqualsCanonicalizing($casts, $this->category->getCasts());
    }

    public function testDates()
    {
        $dates = ['created_at', 'deleted_at', 'updated_at'];
        $this->assertEqualsCanonicalizing($dates, $this->category->getDates());
    }

    public function testNotIncreminting()
    {
        $this->assertFalse($this->category->incrementing);
    }
}
