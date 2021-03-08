<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{

    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];

    public function index()
    {
        return Genre::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        $obj = Genre::create($request->all());
        $obj->refresh();
        return $obj;
    }

    public function show(Genre $genre)
    {
        return $genre;
    }

    public function update(Request $request, Genre $genre)
    {
        $this->validate($request, $this->rules);
        $genre->update($request->all());
        return $genre;
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent();
    }
}
