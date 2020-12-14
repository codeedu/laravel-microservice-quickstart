<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    protected $castMember;
    private $fieldSerialized = [
        'id',
        'name',
        'type'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
            'data' => [
                '*' => $this->fieldSerialized
            ],
            'links' => [],
            'meta' => []
        ]);
        $this->assertResource($response,CastMemberResource::collection(collect([$this->castMember])));
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show',['cast_member' => $this->castMember->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->fieldSerialized
            ]);
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
            'type' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a',256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'type' => 'aa'
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testStore()
    {
        $data = [
            [
                'name' => 'test',
                'type' =>  CastMember::TYPE_DIRECTOR
            ],
            [
                'name' => 'test',
                'type' => CastMember::TYPE_ACTOR
            ]
        ];
        foreach ($data as $key => $value){
            $response = $this->assertStore($value,$value + ['deleted_at' => null]);
            $response->assertJsonStructure([
                'data' => $this->fieldSerialized
            ]);
            $this->assertResource($response,new CastMemberResource(CastMember::find($response->json('data.id'))));
        }
    }

    public function testUpdate()
    {

        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
          'data' => $this->fieldSerialized
        ]);
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('cast_members.destroy',['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));
    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('cast_members.update',['cast_member' => $this->castMember->id ]);
    }

    protected function model()
    {
        return CastMember::class;
    }
}
