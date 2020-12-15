<?php

namespace Tests\Stubs\Controllers;


use App\Http\Controllers\Api\BaseCrudController;
use App\Http\Resources\CategoryResource;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BaseCrudController
{
    protected function model()
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
       return [
           'name' => 'required|max:255',
           'description' => 'nullable'
       ];
    }

    protected function rulesUpdate()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }
    public function resouceCollection()
    {
        return $this->resouce();
    }

    public function resouce()
    {
        return CategoryResource::class;
    }


}
