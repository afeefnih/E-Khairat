<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    use HasFactory;

    protected $fillable = [
         'full_name', 'relationship', 'age', 'ic_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
