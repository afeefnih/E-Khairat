<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'No_Ahli'; // Set the primary key to 'ic_number'

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; // Ensure timestamps are used

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'No_Ahli',
        'ic_number',
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'age',
        'home_phone',
        'residence_status',
    ];

    // Other properties and methods
}
