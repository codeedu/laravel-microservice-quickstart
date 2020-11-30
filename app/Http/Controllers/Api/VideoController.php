<?php

namespace App\Http\Controllers\Api;
use App\Models\Video;
use Illuminate\Http\Request;


class VideoController extends BaseCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:'. implode(',',Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id' => 'required|array|exists:genres,id'
        ];
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request,$this->rulesStore());
        /** @var Video $obj */
        $obj = $this->model()::create($validatedData);
        // O sync limpa tudo e faz de novo
        $obj->categories()->sync($request->get('categories_id'));
        $obj->genres()->sync($request->get('genres_id'));
        $obj->refresh();

        return $obj;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validatedData);
        $obj->categories()->sync($request->get('categories_id'));
        $obj->genres()->sync($request->get('genres_id'));
        return $obj;
    }

    public function model()
    {
        return Video::class;
    }

    public function rulesStore()
    {
        return $this->rules;
    }

    public function rulesUpdate()
    {
        return $this->rules;
    }
}
