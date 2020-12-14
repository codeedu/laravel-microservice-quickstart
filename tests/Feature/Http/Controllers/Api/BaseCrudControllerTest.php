<?php

namespace Tests\Feature\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;
use Illuminate\Http\Request;


class BaseCrudControllerTest extends TestCase
{
    use DatabaseMigrations;
    protected $controller;
    private $fieldSerialize = [
        'id',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * Inicializa o teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    /**
     * Término do teste
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        /** @var  CategoryStub $category */
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'texto']);
        $result = $this->controller->index();
        $data = $result->response()->getData(true);
        $this->assertEquals($category->toArray(), $data['data'][0]);
    }

    /**
     *
     */
    public function testInvalidationDataInStore()
    {
       $this->expectException(ValidationException::class);
        /** Imitação para simular o comportamento */
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test', 'description' => 'test_description']);
        $obj = $this->controller->store($request);
        $data = $obj->response()->getData(true);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $data['data']
        );
    }

    public function testIfFindOrFailFetchModel()
    {
        /** @var  CategoryStub $category */
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'texto']);

        $reflectionClass = new \ReflectionClass(BaseCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }


    public function testIfFindOrFailThrowExceptionWhenIdInvalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new \ReflectionClass(BaseCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'texto']);
        $obj = $this->controller->show($category->id);
        $data = $obj->response()->getData(true);
        $this->assertEquals($data['data'],CategoryStub::find(1)->toArray());
    }

    public function testUpdate()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'texto']);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test', 'description' => 'test_description']);
        $obj = $this->controller->update($request,$category->id );
        $data = $obj->response()->getData(true);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $data['data']
        );
    }

    public function testDestroy()
    {
        $category = CategoryStub::create(['name' => 'teste', 'description' => 'texto']);
        $response = $this->controller->destroy($category->id);
       $this->createTestResponse($response)
           ->assertStatus(204);
       $this->assertCount(0, CategoryStub::all());
    }
}
