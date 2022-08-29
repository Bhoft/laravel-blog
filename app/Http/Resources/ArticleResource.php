<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,

            'expire' => $this->expire,

            'created_dates' => [
                'created_at' => $this->created_at,
                'created_at_human' => $this->created_at->diffForHumans(),
            ],
            'updated_dates' => [
                'updated_at' => $this->updated_at,
                'updated_at_human' => $this->updated_at->diffForHumans(),
            ],
            // 'publication_date_human' => $this->publication_date->diffForHumans(),
            'publish_dates' => !is_null($this->publication_date) ? [
                'publication_date' => $this->publication_date,
                // 'publication_date_human' => $this->publication_date->diffForHumans(),
                'publication_date_human' => \Carbon\Carbon::parse($this->publication_date)->diffForHumans(),
            ] : null,

            // 'publish_dates' => [
            //     'publication_date' => $this->publication_date,
            //     'publication_date_human' => $this->publication_date->diffForHumans(),
            // ],

            // perhaps through attribute
            'is_live' =>  !is_null($this->publication_date) ?  true : false,

            'user' => new UserResource($this->user),
            // 'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
