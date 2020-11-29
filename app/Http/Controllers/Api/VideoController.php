<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends BaseCrudController
{
    private $rules;

    public function model()
    {
        return Video::class;
    }


}
