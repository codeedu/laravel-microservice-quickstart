<?php

namespace Tests\Feature\Http\Controllers\Api;
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
}
