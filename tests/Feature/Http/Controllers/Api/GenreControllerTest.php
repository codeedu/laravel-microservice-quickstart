<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response->assertStatus(200)->assertJson($genre->toArray());
    }

    public function testInvalidationDataPost()
    {
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertInvalidationRequired($response);
        $response = $this->json('POST', route('genres.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'true',
        ]);
        $this->assertInvalidationName($response);
        $this->assertInvalidationIsActive($response);
    }

    public function testInvalidationDataPut()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), []);
        $this->assertInvalidationRequired($response);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
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
        $response = $this->json('POST', route('genres.store'), $values);
        $id = $response->json('id');
        $genre = Genre::find($id);
        $response->assertStatus(201)
        ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertEquals($values['name'], $response->json('name'));
        $newValues = [
            'name' => 'test2',
            'is_active' => false,
        ];
        $response = $this->json('POST', route('genres.store'), $newValues);
        $id = $response->json('id');
        $genre = Genre::find($id);
        $response->assertStatus(201)
        ->assertJson($genre->toArray());
        $this->assertFalse($response->json('is_active'));
        $this->assertEquals($newValues['name'], $response->json('name'));
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false,
        ]);
        $newValues = [
            'name' => 'test 1',
            'is_active' => true,
        ];
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), $newValues);
        $genre = Genre::find($genre->id);
        $response->assertStatus(200)
        ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertEquals($newValues['name'], $response->json('name'));
    }

    public function testDelete()
    {
        $genre = factory(Genre::class)->create();
        $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]));
        $genre = Genre::find($genre->id);
        $this->assertNull($genre);
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
