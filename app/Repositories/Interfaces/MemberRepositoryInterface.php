<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface MemberRepositoryInterface extends AbstractRepositoryInterface
{
    public function fetch_g5_customers();

    public function store(array $data, $balance=null);

    public function index($limit);

    public function all_members($limit=null);

    public function resend_activation_link(User $user);

    public function update_user(User $user, array $data);
}