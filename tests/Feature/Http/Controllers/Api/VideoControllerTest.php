<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));
        $response->assertStatus(200)->assertJson([$this->video->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));
        $response->assertStatus(200)->assertJson($this->video->toArray());
    }

    public function testInvalidationDataPost()
    {
        $this->assertInvalidationInStore(['title' => ''], 'required');
        $this->assertInvalidationInStore(['description' => ''], 'required');
        $this->assertInvalidationInStore(['rating' => ''], 'required');
        $this->assertInvalidationInStore(['genres_id' => ''], 'required');
        $this->assertInvalidationInStore(['genres_id' => 'test'], 'array');
        $this->assertInvalidationInStore(['genres_id' => [100]], 'exists');
        $this->assertInvalidationInStore(['categories_id' => ''], 'required');
        $this->assertInvalidationInStore(['categories_id' => 'test'], 'array');
        $this->assertInvalidationInStore(['categories_id' => [100]], 'exists');
        $this->assertInvalidationInStore(['year_launched' => ''], 'required');
        $this->assertInvalidationInStore(['year_launched' => '01/01/2000'], 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInStore(['duration' => ''], 'required');
        $this->assertInvalidationInStore(['title' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInStore(['opened' => 'true'], 'boolean');
        $this->assertInvalidationInStore(['rating' => 'na'], 'in');
    }

    public function testInvalidationDataPut()
    {
        $this->assertInvalidationInUpdate(['title' => ''], 'required');
        $this->assertInvalidationInUpdate(['description' => ''], 'required');
        $this->assertInvalidationInUpdate(['rating' => ''], 'required');
        $this->assertInvalidationInUpdate(['genres_id' => ''], 'required');
        $this->assertInvalidationInUpdate(['genres_id' => 'test'], 'array');
        $this->assertInvalidationInUpdate(['genres_id' => [100]], 'exists');
        $this->assertInvalidationInUpdate(['categories_id' => ''], 'required');
        $this->assertInvalidationInUpdate(['categories_id' => 'test'], 'array');
        $this->assertInvalidationInUpdate(['categories_id' => [100]], 'exists');
        $this->assertInvalidationInUpdate(['year_launched' => ''], 'required');
        $this->assertInvalidationInUpdate(['year_launched' => '01/01/2000'], 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdate(['duration' => ''], 'required');
        $this->assertInvalidationInUpdate(['title' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdate(['opened' => 'true'], 'boolean');
        $this->assertInvalidationInUpdate(['rating' => 'na'], 'in');
    }

    public function testStore() {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $values = [
            'title' => 'test1',
            'description' => 'description1',
            'year_launched' => 2000,
            'rating' => Video::RATING_FREE,
            'duration' => 120,
            'opened' => true,
        ];
        $response = $this->assertStore($values + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]], $values + ['deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $values = [
            'title' => 'test2',
            'description' => 'description 1',
            'year_launched' => 2020,
            'rating' => '12',
            'duration' => 120,
            'opened' => false,
        ];
        $this->assertStore($values + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]], $values + ['deleted_at' => null]);
        
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $newValues = [
            'title' => 'test1',
            'description' => 'description 1',
            'year_launched' => 2020,
            'rating' => '12',
            'duration' => 120,
            'opened' => true,
        ];
        $response = $this->assertUpdate($newValues + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]], $newValues);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $newValues = [
            'title' => 'test 2',
            'description' => 'description 2',
            'year_launched' => 2020,
            'rating' => '18',
            'duration' => 120,
            'opened' => false,
        ];
        $response = $this->assertUpdate($newValues + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]], $newValues);

        $this->video->description = 'test description';
        $this->video->save();
        $newValues = [
            'title' => 'test description',
            'description' => 'test description',
            'year_launched' => 1985,
            'rating' => '16',
            'duration' => 200,
            'opened' => true,
        ];
        $response = $this->assertUpdate($newValues + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]], $newValues);
    }

    public function testRollbackStore(): void
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $values = [
            'title' => 'test1',
            'description' => 'description1',
            'year_launched' => 2000,
            'rating' => Video::RATING_FREE,
            'duration' => 120,
            'opened' => true,
        ];
        $controller = \Mockery::mock(VideoController::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('handleRelations')->once()->andThrows(new TestException('intencional'));
        $controller->shouldReceive('validate')->withAnyArgs()->andReturn($values);
        $controller->shouldReceive('rulesStore')->withAnyArgs()->andReturn([]);
        $request = \Mockery::mock(Request::class);
        try {
            $controller->store($request);
        } catch(TestException $e) {
            $this->assertCount(1, Video::all());
        }
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $this->AssertInvalidationFields($response, ['title', 'description'], 'required', []);
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function routeStore(): string
    {
        return route('videos.store');
    }

    protected function routeUpdate(): string
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model(): string
    {
        return Video::class;
    }
}
