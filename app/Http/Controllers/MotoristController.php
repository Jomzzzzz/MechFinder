<?php

namespace App\Http\Controllers;

use App\Events\DispatchMessageSent;
use App\Events\DispatchRequestCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MotoristController extends Controller
{
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
      ->join("shop_statuses", "shops.status_id", "=", "shop_statuses.id")
      ->leftJoin("reviews", "shops.id", "=", "reviews.shop_id")
      ->select(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        "shops.latitude",
        "shops.longitude",
        "shop_statuses.slug as status",
        "shops.logo",
        "shops.cover_photo",
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
        "shop_statuses.slug",
        "shops.logo",
        "shops.cover_photo"
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

    return response()
      ->json($shops)
      ->header("Cache-Control", "no-store, no-cache, must-revalidate");
  }

  public function showShop(int $id)
  {
    $shop = DB::table("shops")
      ->join("shop_statuses", "shops.status_id", "=", "shop_statuses.id")
      ->where("shops.id", $id)
      ->select("shops.*", "shop_statuses.slug as status")
      ->first();

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
      "guest_token" => "nullable|string|max:100",
      "owner_name" => "required|string|max:150",
      "contact_number" => "required|string|max:50",
      "vehicle_make_model" => "nullable|string|max:150",
      "vehicle_variant_color" => "nullable|string|max:150",
      "plate_temp_number" => "nullable|string|max:80",
      "issue_type" => "required|string|max:150",
      "description" => "nullable|string",
      "location" => "nullable|string|max:255",
      "latitude" => "nullable|numeric",
      "longitude" => "nullable|numeric",
    ]);

    // Persist guest identity in guest_profiles (normalised away from dispatch_requests)
    if (!empty($validated["guest_token"])) {
      DB::table("guest_profiles")->updateOrInsert(
        ["guest_token" => $validated["guest_token"]],
        [
          "owner_name" => $validated["owner_name"],
          "contact_number" => $validated["contact_number"],
          "vehicle_make_model" => $validated["vehicle_make_model"] ?? null,
          "vehicle_variant_color" => $validated["vehicle_variant_color"] ?? null,
          "plate_temp_number" => $validated["plate_temp_number"] ?? null,
          "updated_at" => now(),
        ]
      );
    }

    // No pre-assignment — first shop to accept claims the job
    $id = DB::table("dispatch_requests")->insertGetId([
      "shop_id" => null,
      "guest_token" => $validated["guest_token"] ?? null,
      "guest_name" => $validated["owner_name"],
      "vehicle_make_model" => $validated["vehicle_make_model"] ?? null,
      "vehicle_variant_color" => $validated["vehicle_variant_color"] ?? null,
      "plate_temp_number" => $validated["plate_temp_number"] ?? null,
      "issue_type" => $validated["issue_type"],
      "description" => $validated["description"] ?? null,
      "location" => $validated["location"] ?? null,
      "latitude" => $validated["latitude"] ?? null,
      "longitude" => $validated["longitude"] ?? null,
      "status" => "requested",
      "price" => 0,
      "created_at" => now(),
      "updated_at" => now(),
    ]);

    // Check if any open shops exist at all
    $openStatusId = DB::table("shop_statuses")
      ->where("slug", "open")
      ->value("id");
    $hasOpenShops = DB::table("shops")
      ->where("status_id", $openStatusId)
      ->exists();

    // Broadcast to ALL shop dashboards — first to accept wins
    broadcast(
      new DispatchRequestCreated([
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
    );

    return response()->json([
      "success" => true,
      "request_id" => $id,
      "shop_found" => $hasOpenShops,
      "message" => $hasOpenShops
        ? "Your rescue request has been sent to nearby shops."
        : "No open shops available right now. Your request has been saved.",
    ]);
  }

  protected function findNearestOpenShop(
    ?float $lat,
    ?float $lng,
    array $excludeIds = []
  ): ?int {
    $openStatusId = DB::table("shop_statuses")
      ->where("slug", "open")
      ->value("id");

    $query = DB::table("shops")->where("status_id", $openStatusId);

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

  public function requestStatus(int $id)
  {
    $request = DB::table("dispatch_requests")
      ->leftJoin("shops", "dispatch_requests.shop_id", "=", "shops.id")
      ->leftJoin(
        "dispatch_mechanics as dm",
        "dm.dispatch_request_id",
        "=",
        "dispatch_requests.id"
      )
      ->leftJoin("users as mu", "mu.id", "=", "dm.mechanic_id")
      ->leftJoin("mechanic_profiles as mp", "mp.user_id", "=", "dm.mechanic_id")
      ->where("dispatch_requests.id", $id)
      ->select(
        "dispatch_requests.id",
        "dispatch_requests.shop_id",
        "dispatch_requests.motorist_id",
        "dispatch_requests.status",
        "dispatch_requests.issue_type",
        "dispatch_requests.location",
        "dispatch_requests.created_at",
        "dispatch_requests.updated_at",
        "shops.shop_name as shop_name",
        "shops.phone as shop_phone",
        "shops.address as shop_address",
        "shops.latitude as shop_lat",
        "shops.longitude as shop_lng",
        "mu.name as mechanic_name",
        "mp.phone as mechanic_phone",
        "mp.plate_number as mechanic_plate"
      )
      ->first();

    if (!$request) {
      return response()->json(["error" => "Request not found."], 404);
    }

    return response()->json($request);
  }

  public function requests()
  {
    $requests = DB::table("dispatch_requests")
      ->where("motorist_id", Auth::id())
      ->orderByDesc("created_at")
      ->get();

    return view("motorist.requests", compact("requests"));
  }

  public function cancelDispatch(int $id)
  {
    $updated = DB::table("dispatch_requests")
      ->where("id", $id)
      ->where("status", "requested")
      ->update(["status" => "cancelled", "updated_at" => now()]);

    if (!$updated) {
      return response()->json(
        ["error" => "Cannot cancel — request not found or already accepted."],
        422
      );
    }

    $shopId = DB::table('dispatch_requests')->where('id', $id)->value('shop_id');
    event(new \App\Events\DispatchStatusUpdated($id, "cancelled", $shopId));

    return response()->json(["success" => true]);
  }

  public function changePassword(Request $request)
  {
    $request->validate([
      "current_password" => ["required"],
      "password" => ["required", "string", "min:6", "confirmed"],
    ]);

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
      return back()->withErrors([
        "current_password" => "Current password is incorrect.",
      ]);
    }

    $user->update(["password" => Hash::make($request->password)]);

    return redirect()
      ->route("motorist.index")
      ->with("pw_success", "Password updated successfully.");
  }

  public function storeReview(Request $request)
  {
    $validated = $request->validate([
      "shop_id" => "required|exists:shops,id",
      "dispatch_id" => "nullable|exists:dispatch_requests,id",
      "rating" => "required|integer|min:1|max:5",
      "comment" => "nullable|string",
      "services" => "nullable|string",
    ]);

    DB::table("reviews")->insert([
      "shop_id" => $validated["shop_id"],
      "dispatch_id" => $validated["dispatch_id"] ?? null,
      "motorist_id" => Auth::id(),
      "rating" => $validated["rating"],
      "comment" => $validated["comment"] ?? null,
      "services" => $validated["services"] ?? null,
      "created_at" => now(),
      "updated_at" => now(),
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
      ->join("shop_statuses", "shops.status_id", "=", "shop_statuses.id")
      ->leftJoin("reviews", "shops.id", "=", "reviews.shop_id")
      ->select(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        "shops.latitude",
        "shops.longitude",
        "shop_statuses.slug as status",
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
        "shop_statuses.slug"
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

  public function chat(int $dispatchId)
  {
    $dispatch = DB::table("dispatch_requests")
      ->leftJoin("shops", "dispatch_requests.shop_id", "=", "shops.id")
      ->leftJoin("dispatch_mechanics as dm", "dm.dispatch_request_id", "=", "dispatch_requests.id")
      ->leftJoin("users as mu", "mu.id", "=", "dm.mechanic_id")
      ->where("dispatch_requests.id", $dispatchId)
      ->where(function ($q) {
        // Allow if motorist_id matches OR if the request was originally made as guest
        // but the user is now authenticated (motorist_id may be null for older requests)
        $q->where("dispatch_requests.motorist_id", Auth::id())
          ->orWhereNull("dispatch_requests.motorist_id");
      })
      ->select("dispatch_requests.*", "shops.shop_name", "mu.name as mechanic_name")
      ->orderByDesc("dm.id")
      ->first();

    if (!$dispatch) {
      abort(404);
    }

    return view("motorist.chat", compact("dispatch"));
  }

  public function getMessages(int $dispatchId)
  {
    $conversationType = request()->query('conversation_type');

    $dispatch = DB::table('dispatch_requests')->where('id', $dispatchId)->firstOrFail();
    if ($dispatch->motorist_id) {
      if (!Auth::check() || Auth::user()->role !== 'motorist' || $dispatch->motorist_id !== Auth::id()) {
        abort(403);
      }
    } else {
      $guestToken = request()->query('guest_token');
      if (!$guestToken || $dispatch->guest_token !== $guestToken) {
        abort(403);
      }
    }

    $query = DB::table("messages")
      ->leftJoin("users as m", "messages.motorist_id", "=", "m.id")
      ->leftJoin("shops", "messages.shop_id", "=", "shops.id")
      ->select(
        "messages.*",
        DB::raw(
          "CASE WHEN messages.sender_type = 'motorist' THEN COALESCE(m.name, 'Motorist') WHEN messages.sender_type = 'shop' THEN COALESCE(shops.shop_name, 'Shop') WHEN messages.sender_type = 'mechanic' THEN 'Mechanic' ELSE 'User' END as sender_name"
        )
      )
      ->where("dispatch_id", $dispatchId);

    if (in_array($conversationType, ['shop', 'motorist', 'mechanic'], true)) {
      $query->where('conversation_type', $conversationType);
      if ($conversationType === 'motorist') {
        $query->where('messages.sender_type', '!=', 'mechanic');
      }
      if ($conversationType === 'mechanic') {
        $query->where('messages.sender_type', '!=', 'shop');
      }
    }

    $messages = $query
      ->orderBy("messages.created_at", "asc")
      ->get()
      ->map(function ($msg) {
        $msg->created_at = Carbon::parse($msg->created_at)->toIso8601String();
        return $msg;
      });

    return response()->json([
      "success" => true,
      "messages" => $messages,
      "current_user_id" => Auth::id(),
    ]);
  }

  public function sendMessage(Request $request)
  {
    $validated = $request->validate([
      "dispatch_id" => "required|exists:dispatch_requests,id",
      "message" => "required|string",
      "sender_type" => "required|in:motorist",
      "conversation_type" => "nullable|in:shop,motorist,mechanic",
      "motorist_id" => "nullable|exists:users,id",
      "shop_id" => "nullable|exists:shops,id",
      "guest_token" => "nullable|string|max:100",
    ]);

    $dispatch = DB::table('dispatch_requests')->where('id', $validated['dispatch_id'])->firstOrFail();
    if ($dispatch->motorist_id) {
      if (!Auth::check() || Auth::user()->role !== 'motorist' || $dispatch->motorist_id !== Auth::id()) {
        abort(403);
      }
      $validated['motorist_id'] = Auth::id();
    } else {
      $guestToken = $validated['guest_token'] ?? null;
      if (!$guestToken || $dispatch->guest_token !== $guestToken) {
        abort(403);
      }
    }

    $senderName = null;
    if ($validated["sender_type"] === "motorist" && !empty($validated["motorist_id"])) {
      $senderName = DB::table("users")
        ->where("id", $validated["motorist_id"])
        ->value("name");
    } elseif ($validated["sender_type"] === "mechanic" && Auth::check()) {
      $senderName = Auth::user()->name;
    } elseif ($validated["sender_type"] === "shop" && !empty($validated["shop_id"])) {
      $senderName = DB::table("shops")
        ->where("id", $validated["shop_id"])
        ->value("shop_name");
    }

    if (!$senderName) {
      if ($validated["sender_type"] === "motorist") {
        $senderName = "Motorist";
      } elseif ($validated["sender_type"] === "shop") {
        $senderName = "Shop";
      } else {
        $senderName = "Mechanic";
      }
    }

    $conversationType = $validated["conversation_type"] ?? 'motorist';

    $insertData = [
      "dispatch_id" => $validated["dispatch_id"],
      "motorist_id" => $validated["motorist_id"] ?? null,
      "shop_id" => $validated["shop_id"] ?? null,
      "message" => $validated["message"],
      "sender_type" => $validated["sender_type"],
      "conversation_type" => $conversationType,
      "created_at" => now(),
      "updated_at" => now(),
    ];

    if ($validated["sender_type"] === "mechanic" && empty($insertData["shop_id"])) {
      $shopId = DB::table("dispatch_requests")
        ->where("id", $validated["dispatch_id"])
        ->value("shop_id");
      $insertData["shop_id"] = $shopId;
    }

    $messageId = DB::table("messages")->insertGetId($insertData);

    $messageData = [
      "id" => $messageId,
      "dispatch_id" => $validated["dispatch_id"],
      "motorist_id" => $insertData["motorist_id"],
      "shop_id" => $insertData["shop_id"],
      "sender_type" => $validated["sender_type"],
      "sender_name" => $senderName,
      "conversation_type" => $conversationType,
      "message" => $validated["message"],
      "created_at" => now()->toIso8601String(),
    ];

    broadcast(new DispatchMessageSent($validated["dispatch_id"], $messageData));

    return response()->json([
      "success" => true,
      "message" => "Message sent successfully.",
      "data" => $messageData,
    ]);
  }

  public function submitReview(Request $request)
  {
    return $this->storeReview($request);
  }

  public function getShopsForMessaging()
  {
    $guestToken = request()->query('guest_token');
    $motoristId = Auth::check() && (Auth::user()?->role === 'motorist') ? Auth::id() : null;

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

    $shops = $shops->map(function ($shop) use ($motoristId, $guestToken) {
      $countQuery = DB::table('shop_messages')
        ->where('shop_id', $shop->id)
        ->where('sender_type', 'shop')
        ->where('is_read', false);

      if ($motoristId) {
        $countQuery->where('motorist_id', $motoristId);
      } elseif ($guestToken) {
        $countQuery->where('guest_token', $guestToken);
      } else {
        $countQuery->whereRaw('1 = 0');
      }

      $shop->unread_count = (int) $countQuery->count('id');
      return $shop;
    });

    return response()->json($shops);
  }

  public function getShopMessages(int $shopId)
  {
    $guest_token = request()->query("guest_token");
    $motorist_id = Auth::check() && (Auth::user()?->role === 'motorist') ? Auth::id() : request()->query("motorist_id");

    $query = DB::table("shop_messages")->where("shop_id", $shopId);

    if ($guest_token) {
      $query->where("guest_token", $guest_token);
    } elseif ($motorist_id) {
      $query->where("motorist_id", $motorist_id);
    } else {
      return response()->json(
        ["error" => "No guest_token or motorist_id provided"],
        400
      );
    }

    $messages = $query->orderBy("created_at", "asc")->get()
        ->map(function ($msg) {
            $msg->created_at = \Carbon\Carbon::parse($msg->created_at)->toIso8601String();
            return $msg;
        });

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
      return response()->json(
        [
          "error" => "Either motorist_id or guest_token must be provided",
        ],
        422
      );
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

  public function getGuestProfile(Request $request)
  {
    $token = $request->query("guest_token");
    $isAuthMotorist = Auth::check() && Auth::user()->role === 'motorist';

    if ($isAuthMotorist) {
      $profile = DB::table("guest_profiles")
        ->where("motorist_id", Auth::id())
        ->first();

      // Bootstrap profile from user account if not yet created
      if (!$profile) {
        $profile = (object) [
          'guest_token'              => 'mf_user_' . Auth::id(),
          'motorist_id'              => Auth::id(),
          'owner_name'               => Auth::user()->name,
          'contact_number'           => null,
          'vehicle_make_model'       => null,
          'vehicle_variant_color'    => null,
          'plate_temp_number'        => null,
          'profile_locked'           => false,
          'profile_change_requested' => false,
          'change_request_reason'    => null,
        ];
      }

      return response()->json(["profile" => $profile]);
    }

    if (!$token) {
      return response()->json(["profile" => null]);
    }

    $profile = DB::table("guest_profiles")
      ->where("guest_token", $token)
      ->first();
    return response()->json(["profile" => $profile]);
  }

  public function saveGuestProfile(Request $request)
  {
    $validated = $request->validate([
      "guest_token"          => "nullable|string|max:100",
      "owner_name"           => "nullable|string|max:150",
      "contact_number"       => "nullable|string|max:50",
      "vehicle_make_model"   => "nullable|string|max:150",
      "vehicle_variant_color"=> "nullable|string|max:150",
      "plate_temp_number"    => "nullable|string|max:80",
    ]);

    $isAuthMotorist = Auth::check() && Auth::user()->role === 'motorist';

    // Determine existing record and enforce lock
    if ($isAuthMotorist) {
      $existing = DB::table("guest_profiles")->where("motorist_id", Auth::id())->first();
    } else {
      if (empty($validated["guest_token"])) {
        return response()->json(["error" => "guest_token required"], 422);
      }
      $existing = DB::table("guest_profiles")->where("guest_token", $validated["guest_token"])->first();
    }

    if ($existing && $existing->profile_locked) {
      return response()->json([
        "error"  => "Profile is locked. Please request a change from admin.",
        "locked" => true,
      ], 423);
    }

    $data = [
      "owner_name"           => $validated["owner_name"] ?? null,
      "contact_number"       => $validated["contact_number"] ?? null,
      "vehicle_make_model"   => $validated["vehicle_make_model"] ?? null,
      "vehicle_variant_color"=> $validated["vehicle_variant_color"] ?? null,
      "plate_temp_number"    => $validated["plate_temp_number"] ?? null,
      "updated_at"           => now(),
    ];

    // Lock the profile once the required fields are provided
    $willBeComplete = !empty($validated["owner_name"]) && !empty($validated["contact_number"]);
    if ($willBeComplete) {
      $data["profile_locked"] = true;
    }

    if ($isAuthMotorist) {
      $authToken = 'mf_user_' . Auth::id();
      DB::table("guest_profiles")->updateOrInsert(
        ["motorist_id" => Auth::id()],
        array_merge($data, ["guest_token" => $authToken])
      );
    } else {
      DB::table("guest_profiles")->updateOrInsert(
        ["guest_token" => $validated["guest_token"]],
        $data
      );
    }

    return response()->json(["success" => true]);
  }

  public function requestProfileChange(Request $request)
  {
    $validated = $request->validate([
      "guest_token" => "nullable|string|max:100",
      "reason"      => "required|string|max:500",
    ]);

    $isAuthMotorist = Auth::check() && Auth::user()->role === 'motorist';

    if ($isAuthMotorist) {
      $updated = DB::table("guest_profiles")
        ->where("motorist_id", Auth::id())
        ->update([
          "profile_change_requested" => true,
          "change_request_reason"    => $validated["reason"],
          "updated_at"               => now(),
        ]);
    } else {
      if (empty($validated["guest_token"])) {
        return response()->json(["error" => "guest_token required"], 422);
      }
      $updated = DB::table("guest_profiles")
        ->where("guest_token", $validated["guest_token"])
        ->update([
          "profile_change_requested" => true,
          "change_request_reason"    => $validated["reason"],
          "updated_at"               => now(),
        ]);
    }

    if (!$updated) {
      return response()->json(["error" => "Profile not found."], 404);
    }

    return response()->json(["success" => true]);
  }

  private function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
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
