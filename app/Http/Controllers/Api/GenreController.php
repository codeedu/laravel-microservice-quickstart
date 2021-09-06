<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GenreController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
    ];

    public function index()
    {
        $data = Genre::query()->paginate(25);

        return response($data);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $data = Genre::create($request->all());

        return response($data);
    }

    public function show(Genre $genre)
    {
        return response($genre);
    }

    public function update(Request $request, Genre $genre)
    {
        $this->validate($request, $this->rules);

        $genre->update($request->all());

        return response($genre);
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return response()->noContent();
    }
}
