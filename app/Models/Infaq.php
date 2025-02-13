<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Infaq extends Model
{
    use HasFactory;

    protected $table = 'infaq';

    protected $fillable = [
        'bill_code',
        'name',
        'email',
        'phone',
        'amount',
        'status',
    ];
}
