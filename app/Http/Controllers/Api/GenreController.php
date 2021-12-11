<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;
use App\Http\Controllers\Api\BasicCrudController;

class GenreController extends BasicCrudController
{
    protected function model(): string
    {
        return Genre::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
        ];
    }
}