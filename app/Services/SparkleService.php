<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SparkleService
{
    private $base_url;
    private $token;

    public function __construct()
    {
        $this->base_url = config('sparkle.api_credentials.base_url');
        $this->token = Cache::remember('sparkle_token', 1200, function(){
            $result = $this->login();
            return $result['data']['token'];
        });
    }

    public function login(){
        $url = $this->base_url."/auth";
        $data = [
            'email' => config('sparkle.api_credentials.email'),
            'password' => config('sparkle.api_credentials.password')
        ];
        $response = Http::post($url, $data);
        return $this->responseHandler($response);
    }

    public function createCustomer(array $data){
        $url = $this->base_url."customer/create";
        $response = Http::withToken($this->token)->post($url, $data);

        return $this->responseHandler($response);
    }

    public function createAccount(array $data){
        $url = $this->base_url."/customer/add-account";
        
        $response = Http::withToken($this->token)->post($url, $data);
        return $this->responseHandler($response);
    }

    public function getCustomers(){
        $url = $this->base_url."/customers";
        $response = Http::withToken($this->token)->get($url);
        return $this->responseHandler($response);
    }

    public function getCustomerAccountwithId(int $id)
    {
        $url = $this->base_url."/fetch-accounts/$id";

        $response = Http::withToken($this->token)->get($url);

        return $this->responseHandler($response);
    }

    public function responseHandler(Response $response){
        if ($response->failed()) {
        return $response->throw()->json();
        }

        return $response->json();
    }
}