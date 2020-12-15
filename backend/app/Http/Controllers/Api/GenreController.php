<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\CastMemberResource;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\Request;


class GenreController extends BaseCrudController
{

    private $rules = [
        'name' => 'required|max:255',
        'is_active' =>'boolean',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL'
    ];

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
        $resource = $this->resouce();
        return new $resource($obj);
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
        $resource = $this->resouce();
        return new $resource($obj);
    }

    protected function handleRelations($genre, Request $request)
    {
        $genre->categories()->sync($request->get('categories_id'));
    }


    protected function model()
    {
        return Genre::class;
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
        return GenreResource::class;
    }
}
