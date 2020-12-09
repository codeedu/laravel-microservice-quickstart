<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CastMember;
use Illuminate\Http\Request;

class CastMemberController extends BaseCrudController
{


    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type'  => 'required|in:'. implode(',',[CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR])
        ];
    }

    protected function model()
    {
        return CastMember::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
