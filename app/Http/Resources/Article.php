<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image_path' => $this->image_path,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'creator' => [
                'user_id' => $this->creator->user_id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ],
            'updater' => [
                'user_id' => $this->updater->user_id,
                'name' => $this->updater->name,
                'email' => $this->updater->email,
            ],
        ];
    }
}
