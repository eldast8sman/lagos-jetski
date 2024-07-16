<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->lastname,
            'photo' => $this->photo,
            'wallet' => $this->wallet()->first(['uuid', 'balance']),
            'username' => $this->username,
            'membership' => $this->membership_information()->first(),
            'last_login' => $this->last_login,
            'prev_login' => $this->prev_login
        ];
    }
}
