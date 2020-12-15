<?php

namespace Tests\Stubs\Models;
use App\Models\Traits\UploadsFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;


class UploadFilesStub extends Model
{
    use UploadsFiles;

    protected $table = 'upload_file_stubs';
    protected $fillable = ['name','file1','file2'];
    public static $fileFields = ['file1','file2'];

    public static function makeTable()
    {
        \Schema::create('upload_file_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('file1')->nullable();
            $table->string('file2')->nullable();
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        \Schema::dropIfExists('upload_file_stubs');
    }

    protected function uploadDir()
    {
        return '1';
    }
}
