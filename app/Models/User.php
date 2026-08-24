<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_admin
 * @property bool $is_superadmin
 * @property bool $is_customer
 * @property bool $is_active
 */
#[Fillable(['name', 'email', 'password', 'is_admin', 'is_superadmin', 'is_customer', 'is_active'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

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
            'is_admin' => 'boolean',
            'is_superadmin' => 'boolean',
            'is_customer' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';

    /**
     * Ensure only one role flag is true at a time and set default is_active.
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            $user->normalizeRoles();

            // Default is_active to true when not set.
            if ($user->is_active === null) {
                $user->is_active = true;
            }
        });
    }

    public function normalizeRoles(): void
    {
        $roles = [
            self::ROLE_SUPERADMIN => (bool) $this->is_superadmin,
            self::ROLE_ADMIN => (bool) $this->is_admin,
            self::ROLE_CUSTOMER => (bool) $this->is_customer,
        ];

        $selectedRole = collect($roles)
            ->filter()
            ->keys()
            ->first();

        $selectedRole ??= self::ROLE_CUSTOMER;

        $this->is_superadmin = $selectedRole === self::ROLE_SUPERADMIN;
        $this->is_admin = $selectedRole === self::ROLE_ADMIN;
        $this->is_customer = $selectedRole === self::ROLE_CUSTOMER;
    }

    /**
     * Convenience methods for role checks and assignment
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isCustomer(): bool
    {
        return (bool) $this->is_customer;
    }

    public function role(): string
    {
        if ($this->isSuperAdmin()) {
            return self::ROLE_SUPERADMIN;
        }

        if ($this->isAdmin()) {
            return self::ROLE_ADMIN;
        }

        return self::ROLE_CUSTOMER;
    }

    public function roleLabel(): string
    {
        return match ($this->role()) {
            self::ROLE_SUPERADMIN => 'Super-admin',
            self::ROLE_ADMIN => 'Admin',
            default => 'Customer',
        };
    }

    public function setRole(string $role): void
    {
        $this->is_superadmin = $role === self::ROLE_SUPERADMIN;
        $this->is_admin = $role === self::ROLE_ADMIN;
        $this->is_customer = $role === self::ROLE_CUSTOMER;
    }

    /**
     * Assign a single role to the user. This will unset other role flags.
     * Accepts: 'superadmin', 'admin', 'customer'
     */
    public function assignRole(string $role): void
    {
        $this->setRole($role);
        $this->save();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    // user has many carts
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    // user has many orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
