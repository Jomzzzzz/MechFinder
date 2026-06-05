<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 */
class MechanicProfile extends Model
{
  protected $fillable = [
    "user_id",
    "shop_id",
    "plate_number",
    "phone",
    "photo",
    "status",
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function shop()
  {
    return $this->belongsTo(Shop::class);
  }
}
