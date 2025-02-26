<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'relationship',
        'age',
        'ic_number'
    ];

    /**
     * Get the user that owns the dependent.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
