<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'custom_amount',  // Add this
        'custom_amount_notes'  // Add this
    ];

    protected $casts = [
        'date_of_death' => 'date',
        'time_of_death' => 'datetime',
        'custom_amount' => 'float',  // Add this to cast to float
    ];

    /**
     * Get the deceased model (User or Dependent).
     */
    public function deceased(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the dependent associated with this death record.
     */
    public function dependent(): BelongsTo
    {
        return $this->belongsTo(Dependent::class, 'dependent_id', 'dependent_id');
    }

    /**
     * Fix the deceased_type attribute on the fly
     */
    protected function deceasedType(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === 'AppModelsUser') {
                    return 'App\\Models\\User';
                }
                if ($value === 'AppModelsDependent') {
                    return 'App\\Models\\Dependent';
                }
                return $value;
            },
            set: function ($value) {
                if ($value === 'AppModelsUser') {
                    return 'App\\Models\\User';
                }
                if ($value === 'AppModelsDependent') {
                    return 'App\\Models\\Dependent';
                }
                return $value;
            },
        );
    }

    /**
     * Get the deceased name regardless of deceased type.
     */
    public function getDeceasedNameAttribute()
    {
        $type = $this->deceased_type;

        if ($type === 'App\\Models\\User' && $this->deceased) {
            return $this->deceased->name ?? 'Unknown';
        }

        if ($type === 'App\\Models\\Dependent' && $this->deceased) {
            return $this->deceased->full_name ?? 'Unknown';
        }

        if ($this->dependent_id && $this->dependent) {
            return $this->dependent->full_name ?? 'Unknown';
        }

        return 'Unknown';
    }

    /**
     * Get the member number (No_Ahli) regardless of deceased type.
     */
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

    /**
     * Get the deceased's age
     */
    public function getDeceasedAgeAttribute()
    {
        $type = $this->deceased_type;

        if ($type === 'App\\Models\\User' && $this->deceased) {
            return $this->deceased->age ?? 0;
        }

        if ($type === 'App\\Models\\Dependent' && $this->deceased) {
            return $this->deceased->age ?? 0;
        }

        return 0;
    }

    /**
     * Get the base death cost based on age category
     */
    public function getBaseCostAttribute()
    {
        $age = $this->deceased_age;

        if ($age <= 3) {
            return 450; // Janin - 3 tahun
        } elseif ($age >= 4 && $age <= 6) {
            return 650; // Kanak-kanak (4-6 tahun)
        } else {
            return 1050; // Dewasa
        }
    }

    /**
     * Get the total death cost (base + custom amount)
     */
    public function getTotalCostAttribute()
    {
        return $this->base_cost + ($this->custom_amount ?? 0);
    }

    /**
     * Get the age category label
     */
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

    /**
     * Get the IC number of the deceased
     */
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
}
