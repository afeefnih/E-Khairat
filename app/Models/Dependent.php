<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependent extends Model
{
    use HasFactory;

    protected $primaryKey = 'dependent_id';

    protected $fillable = ['user_id', 'full_name', 'relationship', 'age', 'ic_number'];

    /**
     * Get the user that owns the dependent.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
   // In your Dependent model
   public function deathRecord(): MorphOne
   {
       return $this->morphOne(DeathRecord::class, 'deceased', 'deceased_type', 'deceased_id', 'dependent_id');
   }

   public function legacyDeathRecord(): HasOne
   {
       return $this->hasOne(DeathRecord::class, 'dependent_id', 'dependent_id');
   }

    /**
     * Check if the dependent is deceased.
     */
    public function isDeceased(): bool
{
    return $this->deathRecord()->exists();
}
}
