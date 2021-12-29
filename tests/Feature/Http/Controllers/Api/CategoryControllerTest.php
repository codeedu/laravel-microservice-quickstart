<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
  
    public function testIndex()
    {
        $response = $this->get(route('categories.index'));
        $response->assertStatus(200)->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));
        $response->assertStatus(200)->assertJson($this->category->toArray());
    }

    public function testInvalidationDataPost()
    {
        $this->assertInvalidationInStore(['name' => ''], 'required');
        $this->assertInvalidationInStore(['name' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInStore(['is_active' => 'true'], 'boolean');
    }

    public function testInvalidationDataPut()
    {
        $this->assertInvalidationInUpdate(['name' => ''], 'required');
        $this->assertInvalidationInUpdate(['name' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdate(['is_active' => 'true'], 'boolean');
    }

    public function testStore() {
        $values = [
            'name' => 'test1',
        ];
        $response = $this->assertStore($values, $values + ['description' => null, 'is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $values = [
            'name' => 'test2',
            'description' => 'description 1',
            'is_active' => false,
        ];
        $this->assertStore($values, $values + ['deleted_at' => null]);
        
    }

    public function testUpdate()
    {
        $newValues = [
            'name' => 'test 1',
            'description' => 'description 2',
            'is_active' => true,
        ];
        $response = $this->assertUpdate($newValues, $newValues);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $newValues = [
            'name' => 'test 1',
            'description' => null,
        ];
        $response = $this->assertUpdate($newValues, $newValues);

        $this->category->description = 'test description';
        $this->category->save();
        $newValues = [
            'name' => 'test description',
            'description' => null,
        ];
        $response = $this->assertUpdate($newValues, $newValues);
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $this->AssertInvalidationFields($response, ['name'], 'required', []);
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function routeStore(): string
    {
        return route('categories.store');
    }

    protected function routeUpdate(): string
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model(): string
    {
        return Category::class;
    }
}
