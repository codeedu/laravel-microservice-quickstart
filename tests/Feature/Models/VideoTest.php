<?php


namespace Tests\Feature\Models;


use App\Models\Video;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    public function testRollbackCreate()
    {
       $hasError = false;
       try{
            Video::create([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2020,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [1,2]
            ]);
        }catch (QueryException $exception){
            $this->assertCount(0,Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $video = factory(Video::class)->create();
        $hasError = false;
        $oldTitle = $video->title;

        try{
            $video->update([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2020,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [1,2]
            ]);
        }catch (QueryException $exception){
            $this->assertDatabaseHas('videos',[
                'title' => $oldTitle
            ]);
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }


}
