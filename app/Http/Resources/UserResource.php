<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
          'name' => $this->name,
          "email" => $this->email,  
          "role" => $this->role->name,
          "image" => asset($this->userProfile->image),
          "created_at" => $this->created_at->format("Y-m-d | H:i:s A"),
          "updated_at" => $this->updated_at->format("Y-m-d | H:i:s A")
        ];
    }
}
