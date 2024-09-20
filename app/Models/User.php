<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    use HasRoles;
    use HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path'
    ];

    //php
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }


    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }
    
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessAdminPanel(): bool
    {
        // Checks if the user has either 'super_admin' or 'Admin' roles
        return $this->hasRole(['super_admin', 'Admin']);
    }
    
    public function canAccessPanel(Panel $panel): bool
    {
        // Checks if the user has any role without specification
        return $this->roles()->exists(); // Ensure 'roles' relationship is correctly defined in your User model
    }
    
}
