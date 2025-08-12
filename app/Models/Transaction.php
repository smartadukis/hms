<?php

namespace App\Models;

use App\Models\User;
use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'account_id',
        'amount',
        'description',
        'invoice_id',
        'receipt_path',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'invoice_id' => 'string',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
