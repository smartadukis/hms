<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'bank_name',
        'account_number',
        'opening_balance',
        'created_by',
        'is_active'
    ];

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class, 'cash_account_id');
    }

    /**
     * compute current balance (opening + deposits - withdrawals)
     * note: transfer logic: deposits to account are positive; withdrawals negative
     */
    public function currentBalance()
    {
        $deposits = $this->transactions()->where('type', 'deposit')->sum('amount');
        $withdrawals = $this->transactions()->where('type', 'withdrawal')->sum('amount');
        // transfers where this account is the destination counted as deposit, where source counted as withdrawal
        // but we already store transfers as separate records below; this is fine.
        return (float)$this->opening_balance + (float)$deposits - (float)$withdrawals;
    }
}
