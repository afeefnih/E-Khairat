<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhairatKematianFund extends Model
{
    use HasFactory;

    // The name of the table this model is associated with
    protected $table = 'khairat_kematian_funds';

    // The attributes that are mass assignable (protection from mass-assignment vulnerabilities)
    protected $fillable = [
        'No_Ahli',
        'bill_code',
        'Payment_amount',
        'payment_status',
        'payment_date',
        'Payment_For',
    ];

    // You can also define relationships here
    // For example, if KhairatKematianFund belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class, 'No_Ahli', 'No_Ahli');
    }
}
