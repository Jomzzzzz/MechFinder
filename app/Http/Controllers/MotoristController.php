<?php

namespace App\Http\Controllers;

use App\Events\DispatchRequestCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotoristController extends Controller
{
  public function dashboard()
  {
    return view("motorist.dashboard");
  }

  public function map()
  {
    return view("motorist.map");
  }

  public function index()
  {
    return view("motorist.index");
  }

  public function shops(Request $request)
  {
    $lat = $request->query("lat");
    $lng = $request->query("lng");

    $shops = DB::table("shops")
      ->leftJoin("reviews", "shops.id", "=", "reviews.shop_id")
      ->select(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        "shops.latitude",
        "shops.longitude",
        "shops.status",
        DB::raw("COALESCE(AVG(reviews.rating), 0) as rating"),
        DB::raw("COUNT(reviews.id) as review_count")
      )
      ->groupBy(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        "shops.latitude",
        "shops.longitude",
        "shops.status"
      )
      ->get()
      ->map(function ($shop) use ($lat, $lng) {
        $shop->rating = round($shop->rating, 1);

        if ($lat && $lng && $shop->latitude && $shop->longitude) {
          $shop->distance = $this->distanceKm(
            $lat,
            $lng,
            $shop->latitude,
            $shop->longitude
          );
          $shop->eta = max(3, round(($shop->distance / 30) * 60));
        } else {
          $shop->distance = null;
          $shop->eta = null;
        }

        return $shop;
      })
      ->sortBy(function ($shop) {
        if ($shop->status === "open") {
          return $shop->distance ?? 999;
        }

        return 9999;
      })
      ->values();

    return response()->json($shops);
  }

  public function showShop($id)
  {
    $shop = DB::table("shops")->where("id", $id)->first();

    if (!$shop) {
      abort(404);
    }

    $reviews = DB::table("reviews")
      ->where("shop_id", $id)
      ->latest()
      ->limit(10)
      ->get();

    return response()->json([
      "shop" => $shop,
      "reviews" => $reviews,
    ]);
  }

  public function storeDispatch(Request $request)
  {
    $validated = $request->validate([
      "guest_token"           => "nullable|string|max:100",
      "owner_name"            => "required|string|max:150",
      "contact_number"        => "required|string|max:50",
      "vehicle_make_model"    => "nullable|string|max:150",
      "vehicle_variant_color" => "nullable|string|max:150",
      "plate_temp_number"     => "nullable|string|max:80",
      "issue_type"            => "required|string|max:150",
      "description"           => "nullable|string",
      "location"              => "nullable|string|max:255",
      "latitude"              => "nullable|numeric",
      "longitude"             => "nullable|numeric",
    ]);

    // Persist guest identity in guest_profiles (normalised away from dispatch_requests)
    if (!empty($validated["guest_token"])) {
      DB::table("guest_profiles")->updateOrInsert(
        ["guest_token" => $validated["guest_token"]],
        [
          "owner_name"     => $validated["owner_name"],
          "contact_number" => $validated["contact_number"],
          "updated_at"     => now(),
        ]
      );
    }

    // Auto-assign to the nearest open shop — motorist does not choose
    $lat    = isset($validated["latitude"])  ? (float) $validated["latitude"]  : null;
    $lng    = isset($validated["longitude"]) ? (float) $validated["longitude"] : null;
    $shopId = $this->findNearestOpenShop($lat, $lng);

    $id = DB::table("dispatch_requests")->insertGetId([
      "shop_id"              => $shopId,
      "guest_token"          => $validated["guest_token"] ?? null,
      "guest_name"           => $validated["owner_name"],   // kept for COALESCE display queries
      "vehicle_make_model"   => $validated["vehicle_make_model"] ?? null,
      "vehicle_variant_color" => $validated["vehicle_variant_color"] ?? null,
      "plate_temp_number"    => $validated["plate_temp_number"] ?? null,
      "issue_type"           => $validated["issue_type"],
      "description"          => $validated["description"] ?? null,
      "location"             => $validated["location"] ?? null,
      "latitude"             => $validated["latitude"] ?? null,
      "longitude"            => $validated["longitude"] ?? null,
      "status"               => "requested",
      "price"                => 0,
      "created_at"           => now(),
      "updated_at"           => now(),
    ]);

    if ($shopId) {
      broadcast(
        new DispatchRequestCreated((int) $shopId, [
        "id" => $id,
        "issue_type" => $validated["issue_type"],
        "owner_name" => $validated["owner_name"],
        "contact_number" => $validated["contact_number"],
        "vehicle_make_model" => $validated["vehicle_make_model"] ?? null,
        "vehicle_variant_color" => $validated["vehicle_variant_color"] ?? null,
        "plate_temp_number" => $validated["plate_temp_number"] ?? null,
        "description" => $validated["description"] ?? null,
        "location" => $validated["location"] ?? null,
        "status" => "requested",
        "created_at" => now()->toDateTimeString(),
      ])
      )->toOthers();
    }

    return response()->json([
      "success"    => true,
      "request_id" => $id,
      "shop_found" => $shopId !== null,
      "message"    => $shopId
        ? "Your rescue request has been sent. A nearby shop will respond shortly."
        : "No open shops available right now. Your request has been saved.",
    ]);
  }

  protected function findNearestOpenShop(?float $lat, ?float $lng, array $excludeIds = []): ?int
  {
    $query = DB::table("shops")->where("status", "open");

    if (!empty($excludeIds)) {
      $query->whereNotIn("id", $excludeIds);
    }

    $shops = $query->select("id", "latitude", "longitude")->get();

    if ($shops->isEmpty()) {
      return null;
    }

    if (!$lat || !$lng) {
      return $shops->first()->id;
    }

    return $shops
      ->sortBy(function ($shop) use ($lat, $lng) {
        if (!$shop->latitude || !$shop->longitude) {
          return 9999;
        }
        return $this->distanceKm($lat, $lng, $shop->latitude, $shop->longitude);
      })
      ->first()?->id;
  }

  public function requestStatus($id)
  {
    $request = DB::table("dispatch_requests")
      ->leftJoin("shops", "dispatch_requests.shop_id", "=", "shops.id")
      ->where("dispatch_requests.id", $id)
      ->select(
        "dispatch_requests.*",
        "shops.shop_name as shop_name",
        "shops.phone as shop_phone",
        "shops.address as shop_address"
      )
      ->first();

    if (!$request) {
      return response()->json(["error" => "Request not found."], 404);
    }

    return response()->json($request);
  }

  public function cancelDispatch($id)
  {
    $updated = DB::table("dispatch_requests")
      ->where("id", $id)
      ->where("status", "requested")
      ->update(["status" => "cancelled", "updated_at" => now()]);

    if (!$updated) {
      return response()->json(["error" => "Cannot cancel — request not found or already accepted."], 422);
    }

    event(new \App\Events\DispatchStatusUpdated($id, "cancelled"));

    return response()->json(["success" => true]);
  }

  public function storeReview(Request $request)
  {
    $validated = $request->validate([
      "shop_id"     => "required|exists:shops,id",
      "dispatch_id" => "nullable|exists:dispatch_requests,id",
      "rating"      => "required|integer|min:1|max:5",
      "comment"     => "nullable|string",
    ]);

    DB::table("reviews")->insert([
      "shop_id"     => $validated["shop_id"],
      "dispatch_id" => $validated["dispatch_id"] ?? null,
      "rating"      => $validated["rating"],
      "comment"     => $validated["comment"] ?? null,
      "created_at"  => now(),
      "updated_at"  => now(),
    ]);

    return response()->json([
      "success" => true,
      "message" => "Review submitted successfully.",
    ]);
  }

  public function apiShops(Request $request)
  {
    $lat = $request->query("lat");
    $lng = $request->query("lng");

    $shops = DB::table("shops")
      ->leftJoin("reviews", "shops.id", "=", "reviews.shop_id")
      ->select(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        "shops.latitude",
        "shops.longitude",
        "shops.status",
        DB::raw("COALESCE(AVG(reviews.rating), 0) as rating"),
        DB::raw("COUNT(reviews.id) as review_count")
      )
      ->groupBy(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        "shops.latitude",
        "shops.longitude",
        "shops.status"
      )
      ->get()
      ->map(function ($shop) use ($lat, $lng) {
        $shop->rating = round($shop->rating, 1);

        if ($lat && $lng && $shop->latitude && $shop->longitude) {
          $shop->distance = $this->distanceKm(
            $lat,
            $lng,
            $shop->latitude,
            $shop->longitude
          );
          $shop->eta = max(3, round(($shop->distance / 30) * 60));
        } else {
          $shop->distance = null;
          $shop->eta = null;
        }

        return $shop;
      })
      ->sortBy(function ($shop) {
        if ($shop->status === "open") {
          return $shop->distance ?? 999;
        }

        return 9999;
      })
      ->values();

    return response()->json($shops);
  }

  public function createDispatchRequest(Request $request)
  {
    return $this->storeDispatch($request);
  }

  public function getMessages($dispatchId)
  {
    $messages = DB::table("messages")
      ->where("dispatch_id", $dispatchId)
      ->orderBy("created_at", "asc")
      ->get();

    if ($messages->isEmpty()) {
      return response()->json([], 404);
    }

    return response()->json($messages);
  }

  public function sendMessage(Request $request)
  {
    $validated = $request->validate([
      "dispatch_id" => "required|exists:dispatch_requests,id",
      "message" => "required|string",
      "sender_type" => "required|in:motorist,shop",
      "motorist_id" => "nullable|exists:users,id",
      "shop_id" => "nullable|exists:shops,id",
      "guest_token" => "nullable|string|max:100",
    ]);

    DB::table("messages")->insert([
      "dispatch_id" => $validated["dispatch_id"],
      "motorist_id" => $validated["motorist_id"] ?? null,
      "shop_id" => $validated["shop_id"] ?? null,
      "message" => $validated["message"],
      "sender_type" => $validated["sender_type"],
      "created_at" => now(),
      "updated_at" => now(),
    ]);

    return response()->json([
      "success" => true,
      "message" => "Message sent successfully.",
    ]);
  }

  public function submitReview(Request $request)
  {
    return $this->storeReview($request);
  }

  public function getShopsForMessaging()
  {
    $shops = DB::table("shops")
      ->leftJoin("reviews", "shops.id", "=", "reviews.shop_id")
      ->select(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        DB::raw("COALESCE(AVG(reviews.rating), 0) as rating")
      )
      ->groupBy("shops.id", "shops.shop_name", "shops.address", "shops.phone")
      ->get();

    return response()->json($shops);
  }

  public function getShopMessages($shopId)
  {
    $guest_token = request()->query("guest_token");
    $motorist_id = request()->query("motorist_id");

    $query = DB::table("shop_messages")
      ->where("shop_id", $shopId);

    if ($guest_token) {
      $query->where("guest_token", $guest_token);
    } elseif ($motorist_id) {
      $query->where("motorist_id", $motorist_id);
    } else {
      return response()->json(["error" => "No guest_token or motorist_id provided"], 400);
    }

    $messages = $query
      ->orderBy("created_at", "asc")
      ->get();

    return response()->json($messages);
  }

  public function sendShopMessage(Request $request)
  {
    $validated = $request->validate([
      "shop_id" => "required|exists:shops,id",
      "message" => "required|string",
      "sender_type" => "required|in:motorist,shop",
      "motorist_id" => "nullable|exists:users,id",
      "guest_token" => "nullable|string|max:100",
    ]);

    if (!$validated["motorist_id"] && !$validated["guest_token"]) {
      return response()->json([
        "error" => "Either motorist_id or guest_token must be provided",
      ], 422);
    }

    DB::table("shop_messages")->insert([
      "motorist_id" => $validated["motorist_id"] ?? null,
      "guest_token" => $validated["guest_token"] ?? null,
      "shop_id" => $validated["shop_id"],
      "message" => $validated["message"],
      "sender_type" => $validated["sender_type"],
      "created_at" => now(),
      "updated_at" => now(),
    ]);

    return response()->json([
      "success" => true,
      "message" => "Message sent successfully.",
    ]);
  }

  private function distanceKm($lat1, $lon1, $lat2, $lon2)
  {
    $earthRadius = 6371;

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a =
      sin($dLat / 2) * sin($dLat / 2) +
      cos(deg2rad($lat1)) *
        cos(deg2rad($lat2)) *
        sin($dLon / 2) *
        sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return round($earthRadius * $c, 2);
  }
}
