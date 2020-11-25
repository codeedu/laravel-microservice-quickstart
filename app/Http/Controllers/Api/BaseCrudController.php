<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

abstract class BaseCrudController extends Controller
{

    protected abstract function model();

    private $rules = [
        'name' => 'required|max:255',
        'is_active' =>'boolean'
    ];

    public function index()
    {
        return $this->model()::all();
    }


    public function store(Request $request)
    {
        $this->validate($request,$this->rules);
        $category = Category::create($request->all());
        $category->refresh();
        return $category;
    }


    public function show(Category $category)
    {
        return $category;
    }


    public function update(Request $request, Category $category)
    {
        $this->validate($request,$this->rules);
        $category->update($request->all());
        return $category;
    }


    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
