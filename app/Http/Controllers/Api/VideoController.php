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
        $self = $this;
        /** @var Video $obj */
        $obj = \DB::transaction(function() use ($request, $validatedData, $self){
            $obj = $this->model()::create($validatedData);
            $self->handleRelations($obj, $request);
            return $obj;
        });
        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $self = $this;
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj = \DB::transaction(function() use ($request, $validatedData, $self, $obj){
            $obj->update($validatedData);
            $self->handleRelations($obj, $request);
            return $obj;
        });

        return $obj;
    }

    private function handleRelations($video, Request $request)
    {
        $video->categories()->sync($request->get('categories_id'));
        $video->genres()->sync($request->get('genres_id'));
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
