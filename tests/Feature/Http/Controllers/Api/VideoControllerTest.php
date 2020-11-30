<?php

namespace Tests\Feature\Http\Controllers\Api;


use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Http\Request;
use Tests\Exceptions\TestExceptions;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create([
            'opened' => true
        ]);
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2020,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }


    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');
    }

    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a',256)
        ];
        $this->assertInvalidationInStoreAction($data,'max.string',['max' => 255]);
        $this->assertInvalidationInUpdateAction($data,'max.string',['max' => 255]);
    }

    public function testInvalidationYear()
    {
        $data = [
            'year_launched' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'date_format',['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data,'date_format',['format' => 'Y']);
    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's'
        ];
        $this->assertInvalidationInStoreAction($data,'integer');
        $this->assertInvalidationInUpdateAction($data,'integer');
    }

    public function testInvalidationBoolean()
    {
        $data = [
            'opened' => 's'
        ];
        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');
    }

    public function testInvalidationIn()
    {
        $data = [
            'rating' => 0
        ];
        $this->assertInvalidationInStoreAction($data,'in');
        $this->assertInvalidationInUpdateAction($data,'in');
    }

    public function testInvalidationCategoriesField()
    {
        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'array');
        $this->assertInvalidationInUpdateAction($data,'array');

        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');

    }

    public function testInvalidationGenresField()
    {
        $data = [
            'genres_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'array');
        $this->assertInvalidationInUpdateAction($data,'array');

        $data = [
            'genres_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');

    }

    public function testStore()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        $response = $this->assertStore($this->sendData + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
                'opened' => false
            ], $this->sendData + [
                'opened' => false
            ]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $this->assertStore(
            $this->sendData + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
                'opened' => true
            ],
            $this->sendData + [
                'opened' => true
            ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        $response = $this->assertUpdate(
            $this->sendData + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id]
            ]
            , $this->sendData + ['opened' => true]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $this->assertUpdate(
            $this->sendData + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
                'opened' => false

            ],
            $this->sendData + ['opened' => false]);
    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(VideoController::class);
        $controller
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestExceptions());

        $request = \Mockery::mock(Request::class);

        try{
            $controller->store($request);
        }catch (TestExceptions $exception){
            $this->assertCount(1,Video::all());
        }
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show',['video' => $this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('videos.destroy',['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update',['video' => $this->video->id ]);
    }

    protected function model()
    {
        return Video::class;
    }
}
