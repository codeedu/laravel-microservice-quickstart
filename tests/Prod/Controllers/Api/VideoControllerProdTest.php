<?php

namespace Tests\Feature\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Tests\Feature\Http\Controllers\Api\VideoController\BaseVideoControllerTestCase;
use Tests\Traits\TestProd;
use Tests\Traits\TestStorages;
use Tests\Traits\TestUploads;

class VideoControllerProdTest extends BaseVideoControllerTestCase
{
    use DatabaseMigrations, TestProd, TestStorages, TestUploads;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skipTestIfNotProd();
        \Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();
    }

    public function testStoreWithFiles()
    {
        $files = $this->getFiles();

        $response = $this->json('POST', $this->routeStore(), $this->sendData + $files);
        $response->assertStatus(201);
        $this->assertFilesOnPersist($response, $files);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'PUT', $this->routeUpdate(), $this->sendData + $files
        );
        $response->assertStatus(200);
        $this->assertFilesOnPersist($response, $files);

        $newFiles = [
            'thumb_file' => UploadedFile::fake()->create("thumb_file.jpg"),
            'video_file' => UploadedFile::fake()->create("video_file.mp4")
        ];

        $response = $this->json(
            'PUT', $this->routeUpdate(), $this->sendData + $newFiles
        );
        $response->assertStatus(200);
        $this->assertFilesOnPersist(
            $response,
            Arr::except($files, ['thumb_file', 'video_file']) + $newFiles
        );

        $id = $response->json('id');
        \Storage::assertMissing("$id/{$files['thumb_file']->hashName()}");
        \Storage::assertMissing("$id/{$files['video_file']->hashName()}");
    }

    protected function assertFilesOnPersist(TestResponse $response, $files)
    {
        $id = $response->json('id');
        $video = Video::find($id);
        $this->assertFilesExistsInStorage($video, $files);
    }

    protected function getFiles(){
        return [
            'thumb_file' => UploadedFile::fake()->create("thumb_file.jpg"),
            'banner_file' => UploadedFile::fake()->create("banner_file.jpg"),
            'trailer_file' => UploadedFile::fake()->create("trailer_file.mp4"),
            'video_file' => UploadedFile::fake()->create("video_file.mp4")
        ];
    }

    public function model()
    {
        return Video::class;
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }
}
