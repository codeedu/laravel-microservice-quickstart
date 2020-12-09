<?php

namespace App\Http\Controllers\Api;
use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
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
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'genres_id' => [
                'required',
                'array',
                'exists:genres,id,deleted_at,NULL'
            ],
            'video_file' => ''
        ];
    }

    public function store(Request $request)
    {
        $this->addRuleIfGenreHasCategories($request);
        $validatedData = $this->validate($request,$this->rulesStore());
        $obj = $this->model()::create($validatedData);
        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $self = $this;
        $this->addRuleIfGenreHasCategories($request);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validatedData);
        return $obj;
    }

    protected function addRuleIfGenreHasCategories(Request $request)
    {
        $categoriesId = $request->get('categories_id');
        $categoriesId = is_array($categoriesId) ? $categoriesId : [];
        $this->rules['genres_id'][] = new GenresHasCategoriesRule($categoriesId);
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
