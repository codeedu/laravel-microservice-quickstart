<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class CategoryStub extends Model
{
    protected $table = 'category_stubs';
    protected $fillable = ['name', 'description'];

    public static function createTable()
    {
        \Schema::create('category_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        \Schema::dropIfExists('category_stubs');
    }
}
