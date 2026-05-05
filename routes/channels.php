<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

// Private user channel
Broadcast::channel("App.Models.User.{id}", function ($user, $id) {
  return (int) $user->id === (int) $id;
});

// Shop dashboard channel — only the shop owner can listen
Broadcast::channel("shop.{shopId}", function ($user, $shopId) {
  return (int) $user->shop_id === (int) $shopId || $user->role === "admin";
});

// Dispatch request channel — shop owner or the motorist who created the request
Broadcast::channel("dispatch.{dispatchId}", function ($user, $dispatchId) {
  $request = DB::table("dispatch_requests")->where("id", $dispatchId)->first();
  if (!$request) {
    return false;
  }

  $shop = DB::table("shops")->where("id", $request->shop_id)->first();
  if (!$shop) {
    return false;
  }

  return (int) $user->shop_id === (int) $request->shop_id ||
    (int) $user->id === (int) $request->motorist_id ||
    $user->role === "admin";
});
