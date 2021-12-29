<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
            'categories_id' => 'required|array|exists:categories,id',
        ];
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());
        $self = $this;
        $record = \DB::transaction(function () use ($request, $validatedData, $self) {            
            $record = $this->model()::create($validatedData);
            $self->handleRelations($request, $record);
            return $record;
        });
        $record->refresh();
        return $record;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $record = $this->findOrFail($id);
        $self = $this;
        $record = \DB::transaction(function () use ($request, $validatedData, $self, $record) {            
            $record->update($validatedData);
            $self->handleRelations($request, $record);
            return $record;
        });
        $record->refresh();
        return $record;
    }

    protected function handleRelations(Request $request, $record) {
        $record->categories()->sync($request->get('categories_id'));
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