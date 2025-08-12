<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'cash_account_id',
        'type',
        'amount',
        'reference',
        'description',
        'transaction_date',
        'related_account_id',
        'receipt_file',
        'created_by',
        'ip_address'
    ];

    public function account()
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }

    public function relatedAccount()
    {
        return $this->belongsTo(CashAccount::class, 'related_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
