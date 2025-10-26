<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable implements AuthenticatableContract
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
    ];

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

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get the column name for the "remember me" token.
     */
    protected function rememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * Get the name of the unique identifier for the user.
     * Laravel sessions expect this to return the user identifier.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id'; // This will be used as user_id in sessions
    }

    /**
     * Get the unique identifier for the user.
     * Laravel sessions expect this to return the user identifier.
     */
    public function getAuthIdentifier(): mixed
    {
        return $this->getKey(); // Returns the account ID as user_id for sessions
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): ?string
    {
        return $this->getAttribute($this->getRememberTokenName());
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value): void
    {
        $this->setAttribute($this->getRememberTokenName(), $value);
    }
}
