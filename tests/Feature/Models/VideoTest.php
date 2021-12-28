<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Video::class, 1)->create();
        $videos = Video::all();
        $fields = array_keys($videos->first()->getAttributes());
        $this->assertCount(1, $videos);
        $this->assertEqualsCanonicalizing([
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'created_at',
            'deleted_at',
            'updated_at',
            "id",
        ], $fields);
    }

    public function testCreate()
    {
        $video = Video::create([
            'title' => 'test1',
            'description' => 'description 1',
            'duration' => 180,
            'year_launched' => 1990,
            'rating' => '14',
        ]);
        $video->refresh();
        $this->assertEquals('test1', $video->title);
        $this->assertFalse($video->opened);
        $this->assertRegExp('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $video->id);
        $video = Video::create([
            'title' => 'test1',
            'description' => 'description 1',
            'duration' => 180,
            'year_launched' => 1990,
            'rating' => '14',
            'opened' => true,
        ]);
        $video->refresh();
        $this->assertTrue($video->opened);
    }

    public function testUpdate()
    {
        $video = factory(Video::class, 1)->create([
            'title' => 'test1',
            'description' => 'description 1',
            'duration' => 180,
            'year_launched' => 1990,
            'rating' => '14',
            'opened' => true,
        ])->first();

        $data = [
            'title' => 'test update',
            'opened' => false,
            'description' => 'update test',
            'duration' => 120,
            'year_launched' => 2000,
            'rating' => Video::RATING_FREE,
        ];
        $video->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
    }

    public function testDelete()
    {
        $video = factory(Video::class, 1)->create()->first();
        $video->delete();
        $this->assertNull(Video::find($video->id));
    }
}
