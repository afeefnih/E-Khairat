<?php

// app/Models/DeathRecord.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeathRecord extends Model
{
    protected $fillable = [
        'user_id',           // Will be used if the record is related to a user
        'dependent_id',      // Will be used if the record is related to a dependent
        'death_date',        // Date of death
        'death_time',        // Time of death
        'place_of_death',    // Place of death
        'death_cause',       // Cause of death
        'attachment',       // Attachment of the death certificate
        'notes',          // Additional notes
        'created_at',         // Timestamp of record creation
        'updated_at',         // Timestamp of record update



    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dependent()
    {
        return $this->belongsTo(Dependent::class, 'dependent_id');
    }
}
