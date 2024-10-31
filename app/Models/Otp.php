<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $table = 'tbl_otp';

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function scopeValid($query, $email, $otp)
    {
        return $query->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now())
            ->where('is_verified', false);
    }

    public function scopeVerified($query, $email, $otp)
    {
        return $query->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now())
            ->where('is_verified', true);
    }

    public function scopeExpired($query, $email, $otp)
    {
        return $query->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '<', now());
    }

    public function scopeNotVerified($query, $email, $otp)
    {
        return $query->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now())
            ->where('is_verified', false);
    }

    public function scopeNotExpired($query, $email, $otp)
    {
        return $query->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now());
    }

    public function scopeNotVerifiedAndNotExpired($query, $email, $otp)
    {
        return $query->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now())
            ->where('is_verified', false);
    }

    public function scopeVerifiedAndNotExpired($query, $email, $otp)
    {
        return $query->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now())
            ->where('is_verified', true);
    }

    
}
