<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\Exceptions\TestExceptions;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    protected $genre;
    private $fieldSerialized = [
        'id',
        'name',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ]
    ];


    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->fieldSerialized
                ],
                'links' => [],
                'meta' => []
            ]);
        $this->assertResource($response,GenreResource::collection(collect([$this->genre])));
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show',['genre' => $this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' =>  $this->fieldSerialized
            ]);
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
            'categories_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a',256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $data = [
            'is_active' => 'aa'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $category = factory(Category::class)->create();
        $data = [
            'name'  => 'test'
        ];

        $response = $this->assertStore(
            $data + ['categories_id' => [$category->id]],
            $data + ['is_active' => true, 'deleted_at' => null]);

        $response->assertJsonStructure([
            'data' => $this->fieldSerialized
        ]);
        $this->assertHasCategory($response->json('data.id'),$category->id);


        $data = [
            'name'  => 'test',
            'is_active' => false
        ];

        $this->assertStore(
            $data + ['categories_id' => [$category->id]],
            $data + ['is_active' => false]);

    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create();
        $data = [
            'name'  => 'test',
            'is_active' => true
        ];

        $response = $this->assertUpdate(
            $data + ['categories_id' => [$category->id]],
            $data + ['deleted_at' => null]);

        $response->assertJsonStructure([
            'data' => $this->fieldSerialized
        ]);

        $this->assertHasCategory($response->json('data.id'),$category->id);
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('genres.destroy',['genre' => $this->genre->id]));
        $response->assertStatus(204);
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];

        $response = $this->json('POST',$this->routeStore(),$sendData);

        $this->assertDatabaseHas('category_genre',[
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('data.id')
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];

        $response = $this->json(
            'PUT',
            route('genres.update',['genre' => $response->json('data.id')]),
            $sendData
        );

        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('data.id')
        ]);

        $this->assertDatabaseHas('category_genre',[
            'category_id' => $categoriesId[1],
            'genre_id'  => $response->json('data.id')
        ]);

        $this->assertDatabaseHas('category_genre',[
            'category_id' => $categoriesId[2],
            'genre_id'  => $response->json('data.id')
        ]);
    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'teste'
            ]);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestExceptions());

        $request = \Mockery::mock(Request::class);

        $hasError = false;
        try{
            $controller->store($request);
        }catch(TestExceptions $exception){
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'teste'
            ]);

        $controller
            ->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestExceptions());

        $request = \Mockery::mock(Request::class);

        $hasError = false;
        try{
            $controller->update($request,1);
        }catch(TestExceptions $exception){
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    protected function assertHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas('category_genre',[
            'genre_id' => $genreId,
            'category_id' => $categoryId
        ]);
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update',['genre' => $this->genre->id ]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
