<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Models\UploadFilesStub;

class UploadFilesUnitTest extends TestCase
{
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFilesStub();
    }

    public function testUploadFile()
    {
        \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadFiles()
    {
        \Storage::fake();
        $file1 = UploadedFile::fake()->create('video1.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');
        $this->obj->uploadFiles([$file1,$file2]);
        \Storage::assertExists("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
    }


}
