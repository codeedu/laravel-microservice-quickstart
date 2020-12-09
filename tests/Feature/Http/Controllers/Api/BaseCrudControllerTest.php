<?php

namespace Tests\Feature\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;
use Illuminate\Http\Request;


class BaseCrudControllerTest extends TestCase
{
    protected $controller;
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
        $result = $this->controller->index()->toArray();
        $this->assertEquals([$category->toArray()], $result);
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
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray()
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
        $this->assertEquals($obj->toArray(),CategoryStub::find(1)->toArray());
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
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray()
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
