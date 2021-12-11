<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionClass;
use Tests\Stubs\Models\CategoryStub;
use Tests\Stubs\Controllers\CategoryControllerStubs;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase
{
    private $controller;
    
    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStubs();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'description test']);
        $result = $this->controller->index()->toArray();
        $this->assertEquals([$category->toArray()], $result);
    }


    public function testInvalidationDataSore()
    {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        /** @var Request $request */
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);
        $this->controller->store($request);
    }
    
    public function testStore() {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        /** @var Request $request */
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);
        $obj = $this->controller->store($request);
        $this->assertEquals(CategoryStub::find(1)->toArray(), $obj->toArray());
    }

    public function testIfFindOrFailFetchsRecord()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'description test']);
        $reflectionClass = new ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailThrowsExceptionWhenInvalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
    }
}

