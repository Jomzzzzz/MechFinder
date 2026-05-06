<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
