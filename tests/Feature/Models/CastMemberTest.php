<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(CastMember::class, 1)->create();
        $categories = CastMember::all();
        $fields = array_keys($categories->first()->getAttributes());
        $this->assertCount(1, $categories);
        $this->assertEqualsCanonicalizing([
            'name',
            'type',
            'is_active',
            'created_at',
            'deleted_at',
            'updated_at',
            "id",
        ], $fields);
    }

    public function testCreate()
    {
        $castMember = CastMember::create([
            'name' => 'test1',
            'type' => CastMember::TYPE_DYRECTOR,
        ]);
        $castMember->refresh();
        $this->assertEquals('test1', $castMember->name);
        $this->assertEquals(CastMember::TYPE_DYRECTOR, $castMember->type);
        $this->assertTrue($castMember->is_active);
        $this->assertRegExp('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $castMember->id);
        $castMember->refresh();
    }

    public function testUpdate()
    {
        $castMember = factory(CastMember::class, 1)->create([
            'name' => 'test name',
            'type' => CastMember::TYPE_MEMBER,
            'is_active' => false,
        ])->first();

        $data = [
            'name' => 'test update',
            'is_active' => true,
        ];
        $castMember->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }
    }

    public function testDelete()
    {
        $castMember = factory(CastMember::class, 1)->create()->first();
        $castMember->delete();
        $this->assertNull(CastMember::find($castMember->id));
    }
}
