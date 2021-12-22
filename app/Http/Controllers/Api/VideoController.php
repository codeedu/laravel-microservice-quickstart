<?php

namespace App\Http\Controllers\Api;

use App\Models\Video as ModelsVideo;
use App\Video;

class VideoController extends BasicCrudController
{
    protected function model(): string
    {
        return Video::class;
    }

    protected function rulesStore(): array
    {
        return [
            'title' => 'required|max:255',
            'descrption' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', ModelsVideo::RATING_LIST),
            'duration' => 'required|integer',
        ];
    }
}
