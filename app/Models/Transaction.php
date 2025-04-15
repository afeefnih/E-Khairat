<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'description',
        'amount',
        'transaction_date',
        'payment_method',
        'receipt_path',
        'status',
        'user_id', // Add this to fillable
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isIncome(): bool {
        return $this->type === 'pendapatan';
    }

    public function isExpense(): bool {
        return $this->type === 'perbelanjaan';
    }
}
