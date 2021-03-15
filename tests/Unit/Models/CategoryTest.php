<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    private $category;


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
        $this->category = new Category();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // 
    }


    public function testIfUseTraits()
    {

        // $traits = [
        //     SoftDeletes::class,
        //     Uuid::class
        // ];
        // $categoryTraits = array_keys(class_uses(Category::class));
        // $this->assertEquals($traits, $categoryTraits);


        $softdeletes = explode("\\", SoftDeletes::class);
        $countsoftdeletes = count($softdeletes);

        $uuid = explode("\\", Uuid::class);
        $countuuid = count($uuid);

        $traits = [$softdeletes[$countsoftdeletes - 1], $uuid[$countuuid - 1]];
        $softdeletes = '';
        $countsoftdeletes = '';
        $uuid = '';
        $countuuid = '';
        // dd($traits);

        $softdeletes =  explode("\\", array_keys((class_uses(Category::class)))[0]);
        $countsoftdeletes = count($softdeletes);

        $uuid =  explode("\\", array_keys((class_uses(Category::class)))[1]);
        $countuuid = count($uuid);

        $categoryTraits = [$softdeletes[$countsoftdeletes - 1], $uuid[$countuuid - 1]];
        $softdeletes = '';
        $countsoftdeletes = '';
        $uuid = '';
        $countuuid = '';
        // dd($categoryTraits);

        $this->assertEquals($traits, $categoryTraits);
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        };
        $this->assertCount(count($dates), $this->category->getDates());
    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->category->incrementing);
    }
}
