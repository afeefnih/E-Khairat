<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'category_description',
        'category_status'
    ];

    /**
     * Get the payments for the payment category.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_category_id');
    }
}
