<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CateforyCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends BaseCrudController
{

    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' =>'boolean'
    ];

    protected function model()
    {
        return Category::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resouceCollection()
    {
        return $this->resouce();
    }

    protected function resouce()
    {
      return CategoryResource::class;
    }
}
