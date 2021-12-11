<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStubs extends BasicCrudController
{
    protected function model(): string
    {
        return CategoryStub::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable',
        ];
    }
}
