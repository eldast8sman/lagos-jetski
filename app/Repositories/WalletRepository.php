<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Interfaces\WalletRepositoryInterface;

class WalletRepository extends AbstractRepository implements WalletRepositoryInterface
{
    public $errors;

    public function __construct(Wallet $wallet)
    {
        parent::__construct($wallet);
    }

    public function userTransactions(int $id, int $limit=10)
    {
        $wallet = $this->findFirstBy([
            'user_id' => $id
        ]);

        $transactions = $wallet->transactions()->orderBy('id', 'desc')->paginate($limit);
        return $transactions;
    }
}