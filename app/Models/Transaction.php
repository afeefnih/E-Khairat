<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'transaction_date',
        'type',
        'payment_method',
        'receipt_path',
        'status'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function isIncome(): bool {
        return $this->type === 'pendapatan';
    }

    public function isExpense(): bool {
        return $this->type === 'perbelanjaan';
    }
}
