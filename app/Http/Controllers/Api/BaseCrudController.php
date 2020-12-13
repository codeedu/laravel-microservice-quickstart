<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCrudController extends Controller
{
    protected $paginationSize = 15;

    protected abstract function model();

    protected abstract function rulesStore();

    protected abstract function rulesUpdate();

    protected abstract function resouce();

    protected abstract function resouceCollection();

    public function index()
    {
        $data = !$this->paginationSize ? $this->mode()::all() : $this->model()::paginate($this->paginationSize);
        $resourceCollectionClass = $this->resouceCollection();
        $refClass = new \ReflectionClass($this->resouceCollection());
        return $refClass->isSubclassOf(ResourceCollection::class)
            ? new $resourceCollectionClass($data)
            : $resourceCollectionClass::collection($data);
    }


    public function store(Request $request)
    {
        $validatedData = $this->validate($request,$this->rulesStore());
        $obj = $this->model()::create($validatedData);
        $obj->refresh();
        $resource = $this->resouce();
        return new $resource($obj);
    }

    public function show($id)
    {
        $obj = $this->findOrFail($id);
        $resource = $this->resouce();
        return new $resource($obj);
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validatedData);
        $resource = $this->resouce();
        return new $resource($obj);
    }

    public function destroy($id)
    {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent();
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName,$id)->firstOrFail();
    }






}
