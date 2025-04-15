<?php

// app/Models/DeathRecord.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeathRecord extends Model
{
    protected $fillable = [
        'user_id',           // Will be used if the record is related to a user
        'dependent_id',      // Will be used if the record is related to a dependent
        'date_of_death',
        'time_of_death',
        'place_of_death',    // Place of death
        'cause_of_death',
        'death_notes',
        'death_attachment_path',
    ];

    protected $casts = [
        'date_of_death' => 'date',
        'time_of_death' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dependent()
{
    // Foreign key on this DeathRecord table, Owner key on the Dependent table
    return $this->belongsTo(Dependent::class, 'dependent_id', 'dependent_id');
}
}
