<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private $category;

    // public static function setUpBeforeClass(): void
    // {
    //     parent::setup(); // called before the first test
    // }

    protected function setup(): void
    {
        parent::setup(); // called before each test
        $this->category = new Category();
    }

    // protected function teardown(): void
    // {
    //     // things to do before flush
    //     parent::tearDown(); // called after each test
    //     // things to do after flush
    // }

    // public static function tearDownAfterClass(): void
    // {
    //     // things to do before flush
    //     parent::tearDown(); // called after the last test
    //     // things to do after flush
    // }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
        // print_r();
    }

    public function testCasts()
    {
        $casts = ['id' => 'string'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->category->incrementing);
    }

    public function testDatesAttributes()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }
}
