<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;


class CastMemberTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    protected $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));
        $response->assertStatus(200)->assertJson([$this->castMember->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(200)->assertJson($this->castMember->toArray());
    }

    public function testInvalidationDataPost()
    {
        $this->assertInvalidationInStore(['name' => ''], 'required');
        $this->assertInvalidationInStore(['type' => 0], 'in');
        $this->assertInvalidationInStore(['type' => 3], 'in');
        $this->assertInvalidationInStore(['name' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInStore(['is_active' => 'true'], 'boolean');
    }

    public function testInvalidationDataPut()
    {
        $this->assertInvalidationInUpdate(['name' => ''], 'required');
        $this->assertInvalidationInUpdate(['type' => 0], 'in');
        $this->assertInvalidationInUpdate(['type' => 3], 'in');
        $this->assertInvalidationInUpdate(['name' => str_repeat('a', 256)], 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdate(['is_active' => 'true'], 'boolean');
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $this->AssertInvalidationFields($response, ['name', 'type'], 'required', []);
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function routeStore(): string
    {
        return route('cast_members.store');
    }

    protected function routeUpdate(): string
    {
        return route('cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    protected function model(): string
    {
        return CastMember::class;
    }

}
