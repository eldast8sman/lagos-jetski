<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface AdminRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function activate(Request $request);

    public function findByEmail(string $email);

    public function findByVerificationToken(string $token);

    public function forgot_password(Request $request);

    public function reset_password(Request $request);

    public function update_account_details(Request $request);

    public function update_profile(Request $request);
}