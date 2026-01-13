<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Auth\FakeUser;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    private $fakeUsers = [
        [
            'id' => 1,
            'email' => 'admin@gmail.com',
            'password' => '123456',
            'role' => 'admin',
            'name' => 'Admin'
        ],
        [
            'id' => 2,
            'email' => 'user@gmail.com',
            'password' => '123456',
            'role' => 'user',
            'name' => 'User'
        ]
    ];
    public function login(Request $request)
    {
        $user = collect($this->fakeUsers)->first(
            fn($u) =>
            $u['email'] === $request->email &&
                $u['password'] === $request->password
        );

        if (!$user) {
            return response()->json(['message' => 'Sai tài khoản'], 401);
        }

        $fakeUser = new FakeUser(
            (string) $user['id'],
            $user['email'],
            $user['role'],
            $user['name']
        );

        $token = JWTAuth::fromUser($fakeUser);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ]);
    }
}