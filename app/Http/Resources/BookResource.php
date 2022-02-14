<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
          "id" => $this->id,
          "title" => $this->title,
          "description" => $this->description,
          "publisher" => $this->publisher->name,
          "author" => AuthorResource::collection($this->authors)
        ];
//        return parent::toArray($request);
    }
}
