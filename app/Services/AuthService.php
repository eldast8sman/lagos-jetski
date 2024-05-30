<?php

namespace App\Services;

use Exception;

class AuthService
{
    private $guard;
    public function __construct($guard='user-api'){
        $this->guard = $guard;
    }
    
    public function attempt($data) : array|bool
    {
        if(!$token = auth($this->guard)->attempt($data)){
            return false;
        }

        return [
            'token' => $token,
            'type' => 'Bearer',
            'expires' => env('JWT_TTL') * 60
        ];
    }

    public function logout() : void
    {
        auth($this->guard)->logout();
    }

    public function logged_in_user(){
        return auth($this->guard)->user();
    }

    public function login($user) : array|bool
    {
        if(!$token = auth($this->guard)->login($user)){
            return false;
        }

        return [
            'token' => $token,
            'type' => 'Bearer',
            'expires' => env('JWT_TTL') * 60
        ];
    }

    public function refresh_token() : array|bool
    {
        try {
            $token = auth($this->guard)->refresh();

            $data = [
                'token' => $token,
                'type' => 'Bearer',
                'expires' => env('JWT_TTL') * 60
            ];

            return $data;
        } catch(Exception $e){
            return false;
        }
    }
}