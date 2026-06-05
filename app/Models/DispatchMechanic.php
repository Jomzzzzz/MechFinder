<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 */
class DispatchMechanic extends Model
{
  protected $fillable = ["dispatch_request_id", "mechanic_id", "status"];

  public function dispatchRequest()
  {
    return $this->belongsTo(DispatchRequest::class);
  }

  public function mechanic()
  {
    return $this->belongsTo(User::class, "mechanic_id");
  }
}
