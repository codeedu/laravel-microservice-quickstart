<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;
use App\Http\Controllers\Api\BasicCrudController;

class GenreController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
        ];
    }

    protected function model(): string
    {
        return Genre::class;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }
}