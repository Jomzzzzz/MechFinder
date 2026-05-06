<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  /** @use HasFactory<UserFactory> */
  use HasFactory, Notifiable;

  const ROLE_ADMIN = "admin";
  const ROLE_SHOP = "shop";
  const ROLE_MECHANIC = "mechanic";
  const ROLE_MOTORIST = "motorist";

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    "name",
    "email",
    "password",
    "google_id",
    "google_token",
    "google_refresh_token",
    "shop_id",
    "role",
  ];

  public function isAdmin(): bool
  {
    return $this->role === self::ROLE_ADMIN;
  }

  public function isShop(): bool
  {
    return $this->role === self::ROLE_SHOP;
  }

  public function isMechanic(): bool
  {
    return $this->role === self::ROLE_MECHANIC;
  }

  public function isMotorist(): bool
  {
    return $this->role === self::ROLE_MOTORIST;
  }

  public function hasRole(string|array $roles): bool
  {
    return in_array($this->role, (array) $roles);
  }

  public function mechanicProfile()
  {
    return $this->hasOne(MechanicProfile::class);
  }

  public function ownedShop()
  {
    return $this->hasOne(Shop::class, "owner_id");
  }

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = ["password", "remember_token"];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      "email_verified_at" => "datetime",
      "password" => "hashed",
    ];
  }
}
