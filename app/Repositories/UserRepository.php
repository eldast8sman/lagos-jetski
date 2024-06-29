<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    public $errors = "";

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findByVerificationToken(string $token)
    {
        if(!$user = $this->findFirstBy(['verification_token' => $token])){
            return false;
        }

        return $user;
    }

    public function activate(Request $request)
    {
        try {
            if(empty($user = $this->findFirstBy(['verification_token' => $request->token, 'email_verified' => 0]))){
                $this->errors = "Wrong Link";
                return false;
            }
            if(strtotime($user->verification_token_expiry) < time()){
                $this->errors = "Expired Link";
                return false;
            }

            $user->verification_token = null;
            $user->verification_token_expiry = null;
            $user->password = bcrypt($request->password);
            $user->email_verified = 1;
            $this->save($user);

            return $user;
        } catch(Exception $e){
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function findByEmail(string $email)
    {
        $user = $this->findFirstBy(['email' => $email]);
        if(!$user){
            return false;
        }
        return $user;
    }
}