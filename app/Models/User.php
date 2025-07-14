<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Panel;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /**
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\School[] $schools
 */

   
    use HasRoles;
     protected $fillable = [
        'name',
        'email',
        'password',
    ];


    public function schools():belongsToMany
    {
        return $this->belongsToMany(School::class)
            ->withPivot('role', 'invited_by')
            ->withTimestamps();
    }

    // Allow users without passwords to authenticate
    public function getAuthPassword()
    {
        return $this->password ?: '';
    }

    //Filament user interface implementation
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow all users to access the admin panel
        return true;
    }
    
}
