<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeathRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'deceased_type',
        'deceased_id',
        'date_of_death',
        'time_of_death',
        'place_of_death',
        'cause_of_death',
        'death_notes',
        'death_attachment_path',
        'custom_amount',
        'custom_amount_notes',
        'non_member_name',
        'non_member_ic_number',
        'non_member_age',
        'non_member_relationship',
    ];

    protected $casts = [
        'date_of_death' => 'date',
        'time_of_death' => 'datetime',
        'custom_amount' => 'float',
    ];

    /**
     * Get the deceased model (User or Dependent).
     */
    public function deceased(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessors for UI display
    public function getAgeCategoryAttribute()
    {
        $age = $this->deceased_age;

        if ($age <= 3) {
            return 'Janin - 3 tahun';
        } elseif ($age >= 4 && $age <= 6) {
            return 'Kanak-kanak (4-6 tahun)';
        } else {
            return 'Dewasa';
        }
    }

    public function getBaseCostAttribute()
    {
        $age = $this->deceased_age;
        if ($age <= 3) {
            return 450;
        } elseif ($age >= 4 && $age <= 6) {
            return 650;
        } else {
            return 1050;
        }
    }

    public function getTotalCostAttribute()
    {
        return $this->base_cost + ($this->custom_amount ?? 0);
    }

    public function getCalculatedAmountAttribute()
    {
        return $this->base_cost;
    }

    public function getFinalAmountAttribute()
    {
        return $this->total_cost;
    }

    public function getDeceasedNameAttribute()
    {
        $type = $this->deceased_type;

        if ($type === 'App\\Models\\User' && $this->deceased) {
            return $this->deceased->name ?? 'Unknown';
        }

        if ($type === 'App\\Models\\Dependent' && $this->deceased) {
            return $this->deceased->full_name ?? 'Unknown';
        }

        return 'Unknown';
    }

    public function getDeceasedIcNumberAttribute()
    {
        $type = $this->deceased_type;

        if ($type === 'App\\Models\\User' && $this->deceased) {
            return $this->deceased->ic_number ?? 'Tiada';
        }

        if ($type === 'App\\Models\\Dependent' && $this->deceased) {
            return $this->deceased->ic_number ?? 'Tiada';
        }

        return 'Tiada';
    }

    public function getMemberNoAttribute()
    {
        $type = $this->deceased_type;

        // If the deceased is a User
        if ($type === 'App\\Models\\User' && $this->deceased) {
            return $this->deceased->No_Ahli ?? 'Tiada';
        }

        // If the deceased is a Dependent
        if ($type === 'App\\Models\\Dependent' && $this->deceased) {
            // Get the user related to the dependent
            $user = $this->deceased->user;
            return $user ? ($user->No_Ahli ?? 'Tiada') : 'Tiada';
        }

        return 'Tiada';
    }

    public function getDeceasedAgeAttribute()
    {
        $type = $this->deceased_type;

        // For non-member
        if (empty($type) || $type === 'non_member') {
            return $this->non_member_age ?? 0;
        }
        if ($type === 'App\\Models\\User' && $this->deceased) {
            return $this->deceased->age ?? 0;
        }
        if ($type === 'App\\Models\\Dependent' && $this->deceased) {
            return $this->deceased->age ?? 0;
        }
        return 0;
    }
}
