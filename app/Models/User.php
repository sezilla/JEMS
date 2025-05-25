<?php

namespace App\Models;

use Filament\Panel;
use App\Models\Conversation;
use Namu\WireChat\Traits\Chatable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;


class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    use HasFactory, Notifiable;

    use HasRoles;
    use HasPanelShield;
    use Chatable;
    use SoftDeletes;


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

    //wirechat
    public function getCoverUrlAttribute(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }
    public function getDisplayNameAttribute(): ?string
    {
        return $this->name ?? 'user';
    }

    //php

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'users_has_departments', 'user_id', 'department_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'users_has_teams', 'user_id', 'team_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }

    public function checklist()
    {
        return $this->hasMany(ChecklistUser::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(UserTask::class);
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
        if ($panel->getId() === 'admin') {
            return $this->hasRole(config('filament-shield.super_admin.name')) || $this->hasRole(config('filament-shield.admin_hr.name')) || $this->hasRole(config('filament-shield.admin_dep.name')) || $this->hasRole(config('filament-shield.coordinator_user.name'));
        }

        if ($panel->getId() === 'app') {
            return true;
        }

        return false;
    }


    //wirechat 
    public function canCreateChats(): bool
    {
        return $this->hasVerifiedEmail();
    }

    public function canCreateGroups(): bool
    {
        return $this->hasVerifiedEmail() && $this->hasAnyRole([
            'super admin',
            'HR Admin',
            'Department Admin',
            'Coordinator',
            'Team Leader'
        ]);
    }
}
