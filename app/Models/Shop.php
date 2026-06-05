<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 */
class Shop extends Model
{
  protected $fillable = [
    "owner_id",
    "shop_name",
    "address",
    "phone",
    "email",
    "latitude",
    "longitude",
    "location",
    "status_id",
  ];

  public function owner()
  {
    return $this->belongsTo(User::class, "owner_id");
  }

  public function mechanics()
  {
    return $this->hasMany(MechanicProfile::class);
  }

  public function dispatchRequests()
  {
    return $this->hasMany(DispatchRequest::class);
  }
}
