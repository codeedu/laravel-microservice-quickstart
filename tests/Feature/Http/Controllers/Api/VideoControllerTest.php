<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create();
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2020,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }


    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');
    }

    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a',256)
        ];
        $this->assertInvalidationInStoreAction($data,'max.string',['max' => 255]);
        $this->assertInvalidationInUpdateAction($data,'max.string',['max' => 255]);
    }

    public function testInvalidationYear()
    {
        $data = [
            'year_launched' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'date_format',['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data,'date_format',['format' => 'Y']);
    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's'
        ];
        $this->assertInvalidationInStoreAction($data,'integer');
        $this->assertInvalidationInUpdateAction($data,'integer');
    }

    public function testInvalidationBoolean()
    {
        $data = [
            'opened' => 's'
        ];
        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');
    }

    public function testInvalidationIn()
    {
        $data = [
            'rating' => 0
        ];
        $this->assertInvalidationInStoreAction($data,'in');
        $this->assertInvalidationInUpdateAction($data,'in');
    }

    public function testInvalidationCategoriesField()
    {
        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'array');
        $this->assertInvalidationInUpdateAction($data,'array');

        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');

    }

    public function testInvalidationGenresField()
    {
        $data = [
            'genres_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'array');
        $this->assertInvalidationInUpdateAction($data,'array');

        $data = [
            'genres_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');

    }

    public function testStore()
    {
        $response = $this->assertStore($this->sendData, $this->sendData + ['opened' => false]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $this->assertStore(
            $this->sendData + ['opened' => true],
            $this->sendData + ['opened' => true]);
    }

    public function testUpdate()
    {
        $response = $this->assertUpdate($this->sendData, $this->sendData + ['opened' => false]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);
        $this->assertUpdate(
            $this->sendData + ['opened' => true],
            $this->sendData + ['opened' => true]);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show',['video' => $this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }


    public function testDestroy()
    {
        $response = $this->json('DELETE', route('videos.destroy',['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update',['video' => $this->video->id ]);
    }

    protected function model()
    {
        return Video::class;
    }
}
