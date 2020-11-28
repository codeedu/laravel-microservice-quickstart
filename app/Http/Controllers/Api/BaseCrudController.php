<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

abstract class BaseCrudController extends Controller
{

    protected abstract function model();

    protected abstract function rulesStore();



    public function index()
    {
        return $this->model()::all();
    }


    public function store(Request $request)
    {
        $validatedData = $this->validate($request,$this->rulesStore());
        $obj = $this->model()::create($validatedData);
        $obj->refresh();
        return $obj;
    }





}
