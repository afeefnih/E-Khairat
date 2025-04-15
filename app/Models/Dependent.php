<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    use HasFactory;

    protected $primaryKey = 'dependent_id';


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
        return $this->belongsTo(User::class, 'user_id','id');
    }
    public function deathRecord()
    {
        // Foreign key on DeathRecord table, Local key on this Dependent table
        return $this->hasOne(DeathRecord::class, 'dependent_id', 'dependent_id');
    }
public function isDeceased()
{
    return $this->deathRecord()->exists();
}
}
