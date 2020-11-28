<?php

namespace App\Http\Controllers\Api;

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
        $this->rules;
    }

    protected function rulesUpdate()
    {
        $this->rules;
    }


}
