<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $fields = array_keys($genres->first()->getAttributes());
        $this->assertCount(1, $genres);
        $this->assertEqualsCanonicalizing([
            'name',
            'is_active',
            'created_at',
            'deleted_at',
            'updated_at',
            "id",
        ], $fields);
    }

    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'test1',
        ]);
        $genre->refresh();
        $this->assertEquals('test1', $genre->name);
        $this->assertNull($genre->description);
        $this->assertTrue($genre->is_active);
        $this->assertRegExp('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $genre->id);
        $this->assertNull($genre->description);
        $genre = Genre::create([
            'name' => 'test3',
            'is_active' => false,
        ]);
        $genre->refresh();
        $this->assertFalse($genre->is_active);
        $genre = Genre::create([
            'name' => 'test4',
            'is_active' => true,
        ]);
        $genre->refresh();
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class, 1)->create([
            'is_active' => false,
        ])->first();

        $data = [
            'name' => 'test update',
            'is_active' => true,
        ];
        $genre->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        $genre = factory(Genre::class, 1)->create()->first();
        $genre->delete();
        $this->assertNull(Genre::find($genre->id));
    }
}
