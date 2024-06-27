<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'firstname',
        'phone',
        'email',
        'password',
        'dob',
        'gender',
        'marital_status',
        'address',
        'membership_id',
        'exp_date',
        'email_verified',
        'verification_token',
        'verification_token_expiry',
        'token',
        'token_expiry',
        'photo',
        'g5_id',
        'sparkle_id',
        'external_sparkle_reference',
        'account_number',
        'last_synced',
        'notification_token',
        'relationship',
        'parent_id',
        'notifications',
        'can_use'
    ];

    protected $hidden = [
        'verification_token',
        'verification_token_expiry',
        'token',
        'token_expiry',
        'password'
    ];

    protected function casts(): array
    {
        return [
            'verification_token_expiry' => 'datetime',
            'token_expiry' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function membership(){
        return $this->belongsTo(Product::class, 'membership_id', 'id');
    }

    public function membership_information(){
        return $this->hasOne(MembershipInformation::class);
    }

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }
}
