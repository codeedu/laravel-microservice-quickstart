<?php

namespace Tests\Feature\Http\Controllers\Api;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;


class BaseCrudControllerTest extends TestCase
{
    /**
     * Inicializa o teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::createTable();
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
        $controller = new CategoryControllerStub();
        $result = $controller->index()->toArray();
        $this->assertEquals([$category->toArray()], $result);
    }
}
