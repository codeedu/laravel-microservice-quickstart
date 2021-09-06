<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController
{
    private $rules;

    public function __construct() // this is the only way to initialize
    {
        // $this->rules = [
        //     'name' => 'required|max:255',
        //     'type' => 'required|in:' . implode(',', [CastMember::TYPE_ACTOR, CastMember::TYPE_DIRECTOR])
        // ];
    }

    protected function model()
    {
        return Video::class;
    }

    protected function rulesStore()
    {
        //return $this->rules;
    }

    protected function rulesUpdate()
    {
        //return $this->rules;
    }
}
