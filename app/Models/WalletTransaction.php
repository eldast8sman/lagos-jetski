<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'wallet_id',
        'amount',
        'type',
        'is_user_credited',
        'external_reference',
        'payment_processor'
    ];

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
