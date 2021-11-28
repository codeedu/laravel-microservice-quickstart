<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $fields = array_keys($categories->first()->getAttributes());
        $this->assertCount(1, $categories);
        $this->assertEqualsCanonicalizing([
            'name',
            'description',
            'is_active',
            'created_at',
            'deleted_at',
            'updated_at',
            "id",
        ], $fields);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test1',
        ]);
        $category->refresh();
        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertRegExp('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $category->id);
        $category = Category::create([
            'name' => 'test2',
            'description' => null,
        ]);
        $category->refresh();
        $this->assertNull($category->description);
        $category = Category::create([
            'name' => 'test3',
            'is_active' => false,
        ]);
        $category->refresh();
        $this->assertFalse($category->is_active);
        $category = Category::create([
            'name' => 'test4',
            'is_active' => true,
        ]);
        $category->refresh();
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class, 1)->create([
            'description' => 'test description',
            'is_active' => false,
        ])->first();

        $data = [
            'name' => 'test update',
            'description' => 'testing update',
            'is_active' => true,
        ];
        $category->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Category::class, 1)->create()->first();
        $category->delete();
        $this->assertNull(Category::find($category->id));
    }
}
