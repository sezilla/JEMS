<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;
use Filament\Panel;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
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
        'avatar_url',
        'custom_fields',
        'email_verified_at'
    ];

    //php

    protected static function booted(): void
    {
        if(config('filament-shield.member_user.enabled', true)) {

            FilamentShield::createRole(name: config('filament-shield.member_user.name', 'Member'));
            static::created(function ($user) {
                $user->assignRole(config('filament-shield.member_user.name', 'Member'));
            });
            static::deleting(function ($user) {
                $user->removeRole(config('filament-shield.member_user.name', 'Member'));
            });
        }
        FilamentShield::createRole(name: config('filament-shield.admin_user.name', 'Admin'));
        FilamentShield::createRole(name: config('filament-shield.coordinator_user.name', 'Coordinator'));
        FilamentShield::createRole(name: config('filament-shield.leader_user.name', 'Team Leader'));
    }

    // public function usersPanel(): string
    // {
    //     return match ($this->getRoleNames()->first()) {
    //         'Admin' => url('/admin'), // Use `url()` instead of `getUrl()`
    //         default => url('/app'),
    //     };
    // }
    

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'users_has_teams', 'user_id', 'team_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'users_has_departments', 'user_id', 'department_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }
    

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
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
            'custom_fields' => 'array'
        ];
    }
    
    public function canAccessPanel(Panel $panel): bool
    {
        if($panel->getId() === 'admin') {
            return $this->hasRole(config('filament-shield.super_admin.name')) || $this->hasRole(config('filament-shield.admin_user.name')) || $this->hasRole(config('filament-shield.coordinator_user.name'));
        } 

        if($panel->getId() === 'app') {
            return true;
        }

        return false;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    
}
