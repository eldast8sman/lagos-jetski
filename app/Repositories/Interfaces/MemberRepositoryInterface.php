<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface MemberRepositoryInterface extends AbstractRepositoryInterface
{
    public function fetch_g5_customers();

    public function store(array $data);

    public function all_members($limit=null);

    public function resend_activation_link(User $user);
}