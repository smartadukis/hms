<?php
// app/Models/Invoice.php

namespace App\Models;

use App\Models\User;
use App\Models\Patient;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



    class Invoice extends Model
    {
        use HasFactory;

        protected $fillable = [
            'patient_id', 'total_amount', 'status', 'payment_method', 'issued_by'
        ];

        public function patient()
        {
            // Now refers to your Patient model
            return $this->belongsTo(Patient::class, 'patient_id');
        }

        public function issuedBy()
        {
            return $this->belongsTo(User::class, 'issued_by');
        }

        public function items()
        {
            return $this->hasMany(InvoiceItem::class);
        }
    }
