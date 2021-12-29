<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use App\Http\Controllers\Api\GenreController;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
  
    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response->assertStatus(200)->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));
        $response->assertStatus(200)->assertJson($this->genre->toArray());
    }

    public function testInvalidationDataPost()
    {
        $this->assertInvalidationInStore(['categories_id' => ''], 'required');
        $this->assertInvalidationInStore(['categories_id' => 'test'], 'array');
        $this->assertInvalidationInStore(['categories_id' => [100]], 'exists');
        $this->assertInvalidationInStore(['name' => ''], 'required');
        $this->assertInvalidationInStore(['name' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInStore(['is_active' => 'true'], 'boolean');
    }

    public function testInvalidationDataPut()
    {
        $this->assertInvalidationInUpdate(['categories_id' => ''], 'required');
        $this->assertInvalidationInUpdate(['categories_id' => 'test'], 'array');
        $this->assertInvalidationInUpdate(['categories_id' => [100]], 'exists');
        $this->assertInvalidationInUpdate(['name' => ''], 'required');
        $this->assertInvalidationInUpdate(['name' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdate(['is_active' => 'true'], 'boolean');
    }

    public function testRollbackStore(): void
    {
        $values = [
            'name' => 'test1',
        ];
        $controller = \Mockery::mock(GenreController::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('handleRelations')->once()->andThrows(new TestException('intencional'));
        $controller->shouldReceive('validate')->withAnyArgs()->andReturn($values);
        $controller->shouldReceive('rulesStore')->withAnyArgs()->andReturn([]);
        $request = \Mockery::mock(Request::class);
        try {
            $controller->store($request);
        } catch(TestException $e) {
            $this->assertCount(1, Genre::all());
        }
    }

    public function testStore() {
        $values = [
            'name' => 'test1',
        ];
        $category = factory(Category::class)->create();
        $response = $this->assertStore($values + ['categories_id' => [$category->id]], $values + ['is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $values = [
            'name' => 'test2',
            'is_active' => false,
        ];
        $this->assertStore($values + ['categories_id' => [$category->id]], $values + ['deleted_at' => null]);
        
    }

    public function testUpdate()
    {
        $newValues = [
            'name' => 'test 1',
            'is_active' => true,
        ];
        $category = factory(Category::class)->create();
        $response = $this->assertUpdate($newValues + ['categories_id' => [$category->id]], $newValues);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $newValues = [
            'name' => 'test 1',
        ];
        $response = $this->assertUpdate($newValues + ['categories_id' => [$category->id]], $newValues);

        $this->genre->name = 'test2';
        $this->genre->save();
        $newValues = [
            'name' => 'test name',
        ];
        $response = $this->assertUpdate($newValues + ['categories_id' => [$category->id]], $newValues);
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $this->AssertInvalidationFields($response, ['name'], 'required', []);
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function routeStore(): string
    {
        return route('genres.store');
    }

    protected function routeUpdate(): string
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model(): string
    {
        return Genre::class;
    }
}
