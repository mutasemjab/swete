<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'status'            => 'boolean',
    ];

    /** Display name for the first assigned role. */
    public function getRoleLabelAttribute(): string
    {
        return $this->roles->first()?->name ?? __('settings.no_role');
    }

    /** Tailwind gradient classes for the avatar background, derived from email hash. */
    public function getAvatarGradientAttribute(): string
    {
        $gradients = [
            'from-indigo-400 to-indigo-600',
            'from-blue-400 to-blue-600',
            'from-emerald-400 to-emerald-600',
            'from-amber-400 to-amber-600',
            'from-rose-400 to-rose-600',
            'from-violet-400 to-violet-600',
            'from-cyan-400 to-teal-600',
        ];

        return $gradients[abs(crc32($this->email)) % count($gradients)];
    }

    /** Up to two initials from the user's name. */
    public function getInitialsAttribute(): string
    {
        $words    = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_substr($word, 0, 1);
        }

        return $initials;
    }
}
