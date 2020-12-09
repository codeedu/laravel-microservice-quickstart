<?php

namespace Tests\Stubs\Models;
use App\Models\Traits\UploadsFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;


class UploadFilesStub extends Model
{
    use UploadsFiles;
    public static $fileFields = ['file1','file2'];


//    protected $table = 'category_stubs';
//    protected $fillable = ['name','description','is_active'];
//
//    public static function createTable()
//    {
//        \Schema::create('category_stubs', function (Blueprint $table) {
//            $table->bigIncrements('id');
//            $table->string('name');
//            $table->text('description')->nullable();
//            $table->timestamps();
//        });
//    }
//
//    public static function dropTable()
//    {
//        \Schema::dropIfExists('category_stubs');
//    }

    protected function uploadDir()
    {
        return '1';
    }
}
