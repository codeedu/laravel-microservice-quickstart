<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillable()
    {
        $this->assertEquals(['name','description','is_active'], $this->category->getFillable());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $this->categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits,$this->categoryTraits);
    }

    public function testCats()
    {
        $casts = ['id' => 'string','is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->category->getIncrementing());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at','created_at','updated_at'];
        foreach($dates as $date)
        {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates),$this->category->getDates());
    }
}
