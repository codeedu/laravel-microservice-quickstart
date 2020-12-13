<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
                'categories' => CategoryResource::collection($this->categories),
                'genres' => GenreResource::collection($this->genres)
            ];
    }
}
