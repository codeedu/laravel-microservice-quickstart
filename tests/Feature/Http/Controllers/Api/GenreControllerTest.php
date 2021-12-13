<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
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
        $response = $this->assertStore($values, $values + ['is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $values = [
            'name' => 'test2',
            'is_active' => false,
        ];
        $this->assertStore($values, $values + ['deleted_at' => null]);
        
    }

    public function testUpdate()
    {
        $newValues = [
            'name' => 'test 1',
            'is_active' => true,
        ];
        $response = $this->assertUpdate($newValues, $newValues);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $newValues = [
            'name' => 'test 1',
        ];
        $response = $this->assertUpdate($newValues, $newValues);

        $this->genre->name = 'test2';
        $this->genre->save();
        $newValues = [
            'name' => 'test name',
        ];
        $response = $this->assertUpdate($newValues, $newValues);
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
