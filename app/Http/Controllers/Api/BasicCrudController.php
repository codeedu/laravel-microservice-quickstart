<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

abstract class BasicCrudController extends Controller
{

    protected abstract function model(): string;

    protected abstract function rulesStore(): array;

    protected abstract function rulesUpdate(): array;

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());
        $record = $this->model()::create($validatedData);
        $record->refresh();
        return $record;
    }

    protected function findOrFail(string $id)
    {
        $model = $this->model();
        $keyName = (new $model())->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id)
    {
        return $this->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, $this->rulesUpdate());
        $record = $this->findOrFail($id);
        $record->update($request->all());
        $record->refresh();
        return $record;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = $this->findOrFail($id);
        $record->delete();
        return response()->noContent();
    }
}
