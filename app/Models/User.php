<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role_assignments', 'user_id', 'role_slug', 'id', 'role_slug');
    }

    /**
     * Phase 6b: a user holds a capability if any role assigned to them
     * has a grant for it — invariant 3's "multiple sources can grant the
     * same (role, capability) pair" collapses to a simple exists() check
     * one level up, same as Reconciler does for modules.
     */
    public function hasCapability(string $capabilitySlug): bool
    {
        return $this->roles()
            ->whereHas('grants', fn ($query) => $query->where('capability_slug', $capabilitySlug))
            ->exists();
    }
}
