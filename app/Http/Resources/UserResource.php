<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,

            // include the users email only if its the user itself
            $this->mergeWhen(auth()->check() && auth()->id() === $this->id, [
                'email' => $this->email,
            ]),
            'age' => $this->age,
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'created_dates' => [
                'created_at' => $this->created_at,
                'created_at_human' => $this->created_at->diffForHumans(),
            ],
            'updated_dates' => [
                'updated_at' => $this->updated_at,
                'updated_at_human' => $this->updated_at->diffForHumans(),
            ],
        ];
    }
}
