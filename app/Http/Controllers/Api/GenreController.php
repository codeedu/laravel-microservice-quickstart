<?php

namespace App\Http\Controllers\Api;


use App\Models\Genre;


class GenreController extends BaseCrudController
{

    private $rules = [
        'name' => 'required|max:255',
        'is_active' =>'boolean'
    ];

    protected function model()
    {
        return Genre::class;
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
