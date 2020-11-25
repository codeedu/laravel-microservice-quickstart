<?php

namespace Test\Stubs\Controllers;


use Test\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BaseCrudController
{
    protected function model()
    {
        return CategoryStub::class;
    }

}

