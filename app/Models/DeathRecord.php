<?php

// app/Models/DeathRecord.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeathRecord extends Model
{
    protected $fillable = [
        'user_id',           // Will be used if the record is related to a user
        'dependent_id',      // Will be used if the record is related to a dependent
        'name',
        'member_id',
        'date_of_death',
        'cause_of_death',
        'date_of_record',
        'funeral_details',
        'contact_person',
        'contact_phone',
        'address',
        'death_certificate_number',
        'notes',
        'status',
        'attachments',
        'location_of_death',
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
