<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 */
class DispatchRequest extends Model
{
  protected $fillable = [
    "motorist_id",
    "shop_id",
    "issue_type",
    "status",
    "description",
    "latitude",
    "longitude",
    "guest_token",
    "guest_name",
  ];

  public function motorist()
  {
    return $this->belongsTo(User::class, "motorist_id");
  }

  public function shop()
  {
    return $this->belongsTo(Shop::class);
  }

  public function mechanics()
  {
    return $this->hasMany(DispatchMechanic::class);
  }
}
