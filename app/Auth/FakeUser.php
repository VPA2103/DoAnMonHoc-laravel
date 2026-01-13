<?php

namespace App\Auth;

use Tymon\JWTAuth\Contracts\JWTSubject;

class FakeUser implements JWTSubject
{
    public function __construct(
        public string $id,
        public string $email,
        public string $role,
        public string $name
    ) {}

    public function getJWTIdentifier()
    {
        return $this->id;
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'role'  => $this->role,
            'name'  => $this->name,
        ];
    }
}