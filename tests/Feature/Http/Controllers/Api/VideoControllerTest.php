<?php

namespace Tests\Feature\Http\Controllers\Api;


use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\UploadsFiles;
use App\Models\Video;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Http\Request;
use Tests\Exceptions\TestExceptions;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestUploads;

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

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
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

        $genre = factory(Genre::class)->create();
        $genre->delete();
        $data = [
            'genres_id' => [$genre->id]
        ];
        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');

    }

    public function testInvalidationVideoField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            12,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testStore()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);


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
        $genre->categories()->sync($category->id);

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

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($categoriesId);

        $response = $this->json('POST',
            $this->routeStore(),
            $this->sendData + [
                'genres_id' => [$genre->id],
                'categories_id'=> [$categoriesId[0]]
            ]
        );

        $this->assertDatabaseHas('category_video',[
            'category_id' => $categoriesId[0],
            'video_id' => $response->json('id')
        ]);

        $response = $this->json(
            'PUT',
            route('videos.update',['video' => $response->json('id')]),
            $this->sendData + [
                'genres_id' => [$genre->id],
                'categories_id' => [$categoriesId[1], $categoriesId[2]]
            ]
        );

        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $response->json('id')
        ]);

        $this->assertDatabaseHas('category_video',[
            'category_id' => $categoriesId[1],
            'video_id'  => $response->json('id')
        ]);

        $this->assertDatabaseHas('category_video',[
            'category_id' => $categoriesId[2],
            'video_id'  => $response->json('id')
        ]);
    }

    public function testSyncGenres()
    {
        /** @var Collection $genres */
        $genres = factory(Genre::class, 3)->create();
        $genresId = $genres->pluck('id')->toArray();
        $categoryId = factory(Category::class)->create()->id;
        $genres->each(function($genres) use ($categoryId){
           $genres->categories()->sync($categoryId);
        });

        $response = $this->json('POST',
            $this->routeStore(),
            $this->sendData + [
                'genres_id' => [$genresId[0]],
                'categories_id'=> [$categoryId]
            ]
        );


        $this->assertDatabaseHas('genre_video',[
            'genre_id' => $genresId[0],
            'video_id' => $response->json('id')
        ]);

        $response = $this->json(
            'PUT',
            route('videos.update',['video' => $response->json('id')]),
            $this->sendData + [
                'genres_id' => [$genresId[1], $genresId[2]],
                'categories_id' => [$categoryId]
            ]
        );
         $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $response->json('id')
        ]);

        $this->assertDatabaseHas('genre_video',[
            'genre_id' => $genresId[1],
            'video_id'  => $response->json('id')
        ]);

        $this->assertDatabaseHas('genre_video',[
            'genre_id' => $genresId[2],
            'video_id'  => $response->json('id')
        ]);
    }

//    public function testRollbackStore()
//    {
//        $controller = \Mockery::mock(VideoController::class);
//        $controller
//            ->makePartial()
//            ->shouldAllowMockingProtectedMethods();
//
//        $controller
//            ->shouldReceive('validate')
//            ->withAnyArgs()
//            ->andReturn($this->sendData);
//
//        $controller
//            ->shouldReceive('rulesStore')
//            ->withAnyArgs()
//            ->andReturn([]);
//
//        $controller
//            ->shouldReceive('handleRelations')
//            ->once()
//            ->andThrow(new TestExceptions());
//
//        $request = \Mockery::mock(Request::class);
//        $request->shouldReceive('get')
//            ->withAnyArgs()
//            ->andReturnNull();
//        $hasError = false;
//        try{
//            $controller->store($request);
//        }catch (TestExceptions $exception){
//            $this->assertCount(1,Video::all());
//            $hasError = true;
//        }
//        $this->assertTrue($hasError);
//    }
//
//    public function testRollbackUpdate()
//    {
//        $controller = \Mockery::mock(VideoController::class)
//            ->makePartial()
//            ->shouldAllowMockingProtectedMethods();
//
//        $controller
//            ->shouldReceive('findOrFail')
//            ->withAnyArgs()
//            ->andReturn($this->video);
//
//        $controller
//            ->shouldReceive('validate')
//            ->withAnyArgs()
//            ->andReturn([
//                'name' => 'teste'
//            ]);
//
//        $controller
//            ->shouldReceive('rulesUpdate')
//            ->withAnyArgs()
//            ->andReturn([]);
//
//        $controller
//            ->shouldReceive('handleRelations')
//            ->once()
//            ->andThrow(new TestExceptions());
//
//        $request = \Mockery::mock(Request::class);
//        $request->shouldReceive('get')
//            ->withAnyArgs()
//            ->andReturnNull();
//
//
//        $hasError = false;
//        try{
//            $controller->update($request,1);
//        }catch(TestExceptions $exception){
//            $this->assertCount(1, Video::all());
//            $hasError = true;
//        }
//        $this->assertTrue($hasError);
//    }

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

    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
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
