<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'payment_category_id',
        'amount',
        'status_id',
        'billcode',
        'order_id',
        'paid_at',
    ];

    // Relationship: Payment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: Payment belongs to a payment category
    public function payment_category()
    {
        return $this->belongsTo(PaymentCategory::class, 'payment_category_id');
    }
}
