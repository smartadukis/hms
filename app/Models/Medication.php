<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medication extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'generic_name',
        'strength',
        'unit_of_strength',
        'category',
        'dispensing_unit',
        'pack_size',
        'manufacturer',
        'barcode_or_ndc',
        'description',
        'is_controlled',
        'requires_refrigeration',
        'storage_conditions',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
