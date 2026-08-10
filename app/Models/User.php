<?php

namespace App\Models;

use App\Models\Concerns\HasUiPreferences;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPanelShield, HasRoles, HasUiPreferences, LogsActivity, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ui_preferences' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['name', 'email', 'email_verified_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
