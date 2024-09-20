<?php

namespace App\Repositories\Interfaces;

interface WalletRepositoryInterface extends AbstractRepositoryInterface
{
    public function userTransactions(int $id, int $limit=10);
}