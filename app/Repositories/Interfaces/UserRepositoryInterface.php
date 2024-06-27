<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface UserRepositoryInterface extends AbstractRepositoryInterface
{
    public function findByVerificationToken(string $token);

    public function activate(Request $request);

    public function findByEmail(string $email);
}