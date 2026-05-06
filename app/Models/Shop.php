<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    "status",
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
