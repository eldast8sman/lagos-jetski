<?php

namespace App\Repositories\Interfaces;

interface MemberRepositoryInterface extends AbstractRepositoryInterface
{
    public function fetch_g5_customers();

    public function store(array $data);
}