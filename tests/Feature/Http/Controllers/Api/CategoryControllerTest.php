<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)->assertJson($category->toArray());
    }

    public function testInvalidationDataPost()
    {
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertInvalidationRequired($response);
        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'true',
        ]);
        $this->assertInvalidationName($response);
        $this->assertInvalidationIsActive($response);
    }

    public function testInvalidationDataPut()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);
        $this->assertInvalidationRequired($response);
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'true',
        ]);
        $this->assertInvalidationName($response);
        $this->assertInvalidationIsActive($response);
    }

    public function testStore() {
        $values = [
            'name' => 'test1',
        ];
        $response = $this->json('POST', route('categories.store'), $values);
        $id = $response->json('id');
        $category = Category::find($id);
        $response->assertStatus(201)
        ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));
        $this->assertEquals($values['name'], $response->json('name'));
        $newValues = [
            'name' => 'test2',
            'description' => 'description 1',
            'is_active' => false,
        ];
        $response = $this->json('POST', route('categories.store'), $newValues);
        $id = $response->json('id');
        $category = Category::find($id);
        $response->assertStatus(201)
        ->assertJson($category->toArray());
        $this->assertFalse($response->json('is_active'));
        $this->assertEquals($newValues['description'], $response->json('description'));
        $this->assertEquals($newValues['name'], $response->json('name'));
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'is_active' => false,
            'description' => 'description 1',
        ]);
        $newValues = [
            'name' => 'test 1',
            'description' => 'description 2',
            'is_active' => true,
        ];
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), $newValues);
        $category = Category::find($category->id);
        $response->assertStatus(200)
        ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertEquals($newValues['description'], $response->json('description'));
        $this->assertEquals($newValues['name'], $response->json('name'));

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'description' => '',
            'name' => $newValues['name'],
        ]);
        $response->assertStatus(200)->assertJsonFragment(['description' => null]);

        $category->description = 'test description';
        $category->save();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'description' => null,
            'name' => $newValues['name'],
        ]);
        $response->assertStatus(200)->assertJsonFragment(['description' => null]);
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create();
        $this->json('DELETE', route('categories.destroy', ['category' => $category->id]));
        $category = Category::find($category->id);
        $this->assertNull($category);
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name'])
        ->assertJsonMissingValidationErrors(['is_active'])
        ->assertJsonFragment(
            [\Lang::get('validation.required', ['attribute' => 'name'])]
        );
    }

    protected function assertInvalidationName(TestResponse $response) {
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name'])
        ->assertJsonFragment(
            [\Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])]
        );
    }

    protected function assertInvalidationIsActive(TestResponse $response) {
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['is_active'])
        ->assertJsonFragment(
            [\Lang::get('validation.boolean', ['attribute' => 'is active'])]
        );
    }
}
