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
        'death_attachment_path'
    ];
    protected $casts = [
        'date_of_death' => 'date',
        'time_of_death' => 'datetime',
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
    // In your DeathRecord model
    public function dependent(): BelongsTo
    {
        return $this->belongsTo(Dependent::class, 'dependent_id', 'dependent_id'); // Specify the correct foreign key and primary key
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

    // Other accessors...
}
