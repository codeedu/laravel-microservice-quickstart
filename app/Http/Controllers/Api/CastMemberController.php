<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use App\Models\CastMember;

class CastMemberController extends BasicCrudController
{
    protected function model(): string
    {
        return CastMember::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'type' => 'numeric|in:' . implode(',', CastMember::getPossibleMembers()),
            'is_active' => 'boolean',
        ];
    }
}
