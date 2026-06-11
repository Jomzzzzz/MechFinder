<?php

namespace App\Http\Controllers;

use App\Events\DispatchMessageSent;
use App\Events\DispatchStatusUpdated;
use App\Events\ShopStatusUpdated;
use App\Models\MechanicProfile;
use App\Models\DispatchMechanic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ShopController extends Controller
{
  protected function getCurrentShopId()
  {
    $user = Auth::user();

    if ($user && !empty($user->shop_id)) {
      return $user->shop_id;
    }

    $shop = DB::table("shops")->where("owner_id", $user?->id)->first();

    if ($shop) {
      return $shop->id;
    }

    return 1;
  }

  protected function getShop()
  {
    return DB::table("shops")
      ->join("shop_statuses", "shops.status_id", "=", "shop_statuses.id")
      ->where("shops.id", $this->getCurrentShopId())
      ->select("shops.*", "shop_statuses.slug as status")
      ->first();
  }

  protected function getShopStatus(): string
  {
    $shop = $this->getShop();

    return $shop ? strtolower($shop->status) : "closed";
  }

  public function dashboard()
  {
    $shopId = $this->getCurrentShopId();

    // All 'requested' requests are visible to every shop — first to accept wins
    $requests = DB::table("dispatch_requests")
      ->leftJoin(
        "guest_profiles as gp",
        "dispatch_requests.guest_token",
        "=",
        "gp.guest_token"
      )
      ->where("dispatch_requests.status", "requested")
      ->select(
        "dispatch_requests.*",
        "gp.owner_name as owner_name",
        "gp.contact_number as contact_number",
        DB::raw(
          'COALESCE(gp.owner_name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        )
      )
      ->latest("dispatch_requests.created_at")
      ->get();

    $jobs = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin(
        "guest_profiles as gp",
        "dispatch_requests.guest_token",
        "=",
        "gp.guest_token"
      )
      ->leftJoin(
        "dispatch_mechanics as dm",
        "dm.dispatch_request_id",
        "=",
        "dispatch_requests.id"
      )
      ->leftJoin("users as mu", "mu.id", "=", "dm.mechanic_id")
      ->where("dispatch_requests.shop_id", $shopId)
      ->whereIn("dispatch_requests.status", ["accepted", "en_route", "arrived"])
      ->select(
        "dispatch_requests.*",
        "gp.owner_name as owner_name",
        "gp.contact_number as contact_number",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        ),
        "dm.mechanic_id as assigned_mechanic_user_id",
        "mu.name as assigned_mechanic_name"
      )
      ->latest("dispatch_requests.updated_at")
      ->get();

    $pending = $requests->count();

    $jobsToday = DB::table("dispatch_requests")
      ->where("shop_id", $shopId)
      ->whereDate("created_at", now()->toDateString())
      ->count();

    $averageRating = DB::table("reviews")
      ->where("shop_id", $shopId)
      ->avg("rating");

    $averageRating = $averageRating ? round($averageRating, 1) : 0;

    $activeJobsCount = DB::table("dispatch_requests")
      ->where("shop_id", $shopId)
      ->whereIn("status", ["accepted", "en_route", "arrived"])
      ->count();

    $shop = $this->getShop();
    $shopStatus = $this->getShopStatus();
    $shopStatusId = $shop ? (int) $shop->status_id : null;

    $needsProfileCompletion =
      !$shop ||
      empty($shop->shop_name) ||
      empty($shop->address) ||
      empty($shop->phone);

    return view(
      "shop.dashboard",
      compact(
        "requests",
        "jobs",
        "pending",
        "jobsToday",
        "averageRating",
        "activeJobsCount",
        "shop",
        "shopStatus",
        "shopStatusId",
        "needsProfileCompletion"
      )
    );
  }

  public function unclaimedRequests()
  {
    // All requested requests — visible to every shop
    $requests = DB::table("dispatch_requests")
      ->leftJoin(
        "guest_profiles as gp",
        "dispatch_requests.guest_token",
        "=",
        "gp.guest_token"
      )
      ->where("dispatch_requests.status", "requested")
      ->select(
        "dispatch_requests.id",
        "dispatch_requests.issue_type",
        "dispatch_requests.vehicle_make_model",
        "dispatch_requests.vehicle_variant_color",
        "dispatch_requests.plate_temp_number",
        "dispatch_requests.description",
        "dispatch_requests.location",
        "dispatch_requests.latitude",
        "dispatch_requests.longitude",
        "dispatch_requests.created_at",
        "gp.owner_name",
        "gp.contact_number",
        DB::raw(
          'COALESCE(gp.owner_name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        )
      )
      ->latest("dispatch_requests.created_at")
      ->get();

    return response()->json(["requests" => $requests]);
  }

  public function fetchRequests()
  {
    $shopId = $this->getCurrentShopId();

    // Count ALL pending requests (every shop competes)
    $unclaimedCount = DB::table("dispatch_requests")
      ->where("status", "requested")
      ->count();

    return response()->json([
      "success" => true,
      "pending" => $unclaimedCount,
      "shopStatus" => $this->getShopStatus(),
    ]);
  }

  public function requests(Request $request)
  {
    $shopId = $this->getCurrentShopId();

    $status = $request->query("status");

    $query = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin(
        "guest_profiles as gp",
        "dispatch_requests.guest_token",
        "=",
        "gp.guest_token"
      )
      ->leftJoin(
        "dispatch_mechanics as dm",
        "dm.dispatch_request_id",
        "=",
        "dispatch_requests.id"
      )
      ->leftJoin("users as mu", "mu.id", "=", "dm.mechanic_id")
      ->where("dispatch_requests.shop_id", $shopId)
      ->select(
        "dispatch_requests.*",
        "gp.owner_name as owner_name",
        "gp.contact_number as contact_number",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        ),
        "dm.mechanic_id as assigned_mechanic_user_id",
        "mu.name as assigned_mechanic_name"
      );

    // ✅ Apply filter if selected
    if (!empty($status)) {
      $query->where("dispatch_requests.status", $status);
    }

    $data = $query->latest("dispatch_requests.created_at")->get();

    return view("shop.requests", compact("data"));
  }

  public function fetchActiveJobs()
  {
    $shopId = $this->getCurrentShopId();

    $jobs = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin(
        "guest_profiles as gp",
        "dispatch_requests.guest_token",
        "=",
        "gp.guest_token"
      )
      ->where("dispatch_requests.shop_id", $shopId)
      ->whereIn("dispatch_requests.status", ["accepted", "en_route", "arrived"])
      ->select(
        "dispatch_requests.*",
        "gp.owner_name as owner_name",
        "gp.contact_number as contact_number",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        )
      )
      ->latest("dispatch_requests.updated_at")
      ->get();

    return view("components.active-jobs-list", compact("jobs"));
  }

  public function dashboardMapData()
  {
    $shopId = $this->getCurrentShopId();

    $shop = DB::table("shops")
      ->join("shop_statuses", "shops.status_id", "=", "shop_statuses.id")
      ->where("shops.id", $shopId)
      ->select(
        "shops.id",
        "shops.shop_name",
        "shops.address",
        "shops.location",
        "shops.latitude",
        "shops.longitude",
        "shop_statuses.slug as status"
      )
      ->first();

    $requests = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->where("dispatch_requests.shop_id", $shopId)
      ->whereIn("dispatch_requests.status", [
        "requested",
        "accepted",
        "en_route",
        "arrived",
      ])
      ->whereNotNull("dispatch_requests.latitude")
      ->whereNotNull("dispatch_requests.longitude")
      ->where("dispatch_requests.latitude", "!=", "")
      ->where("dispatch_requests.longitude", "!=", "")
      ->select(
        "dispatch_requests.id",
        "dispatch_requests.issue_type",
        "dispatch_requests.description",
        "dispatch_requests.status",
        "dispatch_requests.latitude",
        "dispatch_requests.longitude",
        "dispatch_requests.location",
        "dispatch_requests.guest_name",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        )
      )
      ->latest("dispatch_requests.created_at")
      ->get();

    return response()->json([
      "success" => true,
      "shop" => $shop,
      "requests" => $requests,
    ]);
  }

  public function accept(int $id)
  {
    $shopId = $this->getCurrentShopId();

    // Atomic race — InnoDB serialises concurrent UPDATEs; only the first changes status to 'accepted'
    $claimed = DB::table("dispatch_requests")
      ->where("id", $id)
      ->where("status", "requested")
      ->update([
        "shop_id" => $shopId,
        "status" => "accepted",
        "accepted_at" => now(),
        "updated_at" => now(),
      ]);

    if (!$claimed) {
      return response()->json([
        "success" => false,
        "taken" => true,
        "message" => "Another shop already accepted this request.",
      ]);
    }

    broadcast(new DispatchStatusUpdated($id, "accepted", $shopId));

    return response()->json(["success" => true, "status" => "accepted"]);
  }

  public function decline(int $id)
  {
    // Shop passes on this request — no DB change, stays available for others
    return response()->json(["success" => true]);
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
        return $this->distanceKm(
          $lat,
          $lng,
          (float) $shop->latitude,
          (float) $shop->longitude
        );
      })
      ->first()?->id;
  }

  protected function distanceKm(
    float $lat1,
    float $lon1,
    float $lat2,
    float $lon2
  ): float {
    $R = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a =
      sin($dLat / 2) ** 2 +
      cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    return round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
  }

  public function updateRequestStatus(Request $request, int $id)
  {
    $validated = $request->validate([
      "status" =>
        "required|in:requested,accepted,en_route,arrived,completed,declined",
    ]);

    $updateData = [
      "status" => $validated["status"],
      "updated_at" => now(),
    ];

    if ($validated["status"] === "accepted") {
      $updateData["accepted_at"] = now();
    }

    if ($validated["status"] === "en_route") {
      $hasMechanic = DB::table("dispatch_mechanics")
        ->where("dispatch_request_id", $id)
        ->exists();

      if (!$hasMechanic) {
        return response()->json(
          [
            "success" => false,
            "message" => "Please assign a mechanic before marking as En Route.",
          ],
          422
        );
      }

      $updateData["en_route_at"] = now();
    }

    if ($validated["status"] === "arrived") {
      $updateData["arrived_at"] = now();
    }

    if ($validated["status"] === "completed") {
      $updateData["completed_at"] = now();
    }

    DB::table("dispatch_requests")
      ->where("id", $id)
      ->where("shop_id", $this->getCurrentShopId())
      ->update($updateData);

    if (in_array($validated["status"], ["accepted", "en_route", "arrived"], true)) {
      DispatchMechanic::where("dispatch_request_id", $id)
        ->update(["status" => $validated["status"]]);

      $busyStatusId = DB::table("shop_statuses")
        ->where("slug", "busy")
        ->value("id");

      if ($busyStatusId) {
        DB::table("shops")
          ->where("id", $this->getCurrentShopId())
          ->update(["status_id" => $busyStatusId]);

        broadcast(new ShopStatusUpdated($this->getCurrentShopId(), "busy"));
      }
    }

    if ($validated["status"] === "completed") {
      $assignments = DispatchMechanic::where("dispatch_request_id", $id)->get();
      foreach ($assignments as $assignment) {
        MechanicProfile::where("user_id", $assignment->mechanic_id)
          ->where("shop_id", $this->getCurrentShopId())
          ->update(["status" => "available"]);
      }

      DispatchMechanic::where("dispatch_request_id", $id)
        ->update(["status" => "completed"]);

      $openStatusId = DB::table("shop_statuses")
        ->where("slug", "open")
        ->value("id");

      if ($openStatusId) {
        DB::table("shops")
          ->where("id", $this->getCurrentShopId())
          ->update(["status_id" => $openStatusId]);

        broadcast(new ShopStatusUpdated($this->getCurrentShopId(), "open"));
      }
    }

    broadcast(new DispatchStatusUpdated($id, $validated["status"], $this->getCurrentShopId()));

    return response()->json([
      "success" => true,
      "status" => $validated["status"],
    ]);
  }

  public function uploadImages(Request $request)
  {
    $request->validate([
      "logo" => "nullable|image|mimes:jpeg,jpg,png,webp|max:2048",
      "cover_photo" => "nullable|image|mimes:jpeg,jpg,png,webp|max:4096",
    ]);

    $shopId = $this->getCurrentShopId();
    $shop = $this->getShop();
    $update = ["updated_at" => now()];

    if ($request->hasFile("logo")) {
      if ($shop && $shop->logo) {
        Storage::disk("public")->delete($shop->logo);
      }
      $update["logo"] = $request->file("logo")->store("shops/logos", "public");
    }

    if ($request->hasFile("cover_photo")) {
      if ($shop && $shop->cover_photo) {
        Storage::disk("public")->delete($shop->cover_photo);
      }
      $update["cover_photo"] = $request
        ->file("cover_photo")
        ->store("shops/covers", "public");
    }

    DB::table("shops")->where("id", $shopId)->update($update);

    return back()->with("success", "Shop images updated successfully.");
  }

  public function update(Request $request)
  {
    $shopId = $this->getCurrentShopId();

    $validated = $request->validate([
      "shop_name" => "required|string|max:150",
      "address" => "required|string|max:255",
      "latitude" => "required|numeric",
      "longitude" => "required|numeric",
      "phone" => "nullable|string|max:50",
      "email" => "nullable|email|max:255",
      "status_id" => "required|exists:shop_statuses,id",
    ]);

    DB::table("shops")
      ->where("id", $shopId)
      ->update([
        "shop_name" => $validated["shop_name"],
        "address" => $validated["address"],
        "location" => $validated["address"],
        "latitude" => $validated["latitude"],
        "longitude" => $validated["longitude"],
        "phone" => $validated["phone"] ?? null,
        "email" => $validated["email"] ?? null,
        "status_id" => $validated["status_id"],
        "updated_at" => now(),
      ]);

    return back()->with("success", "Shop settings updated successfully.");
  }

  public function messages()
  {
    $shopId = $this->getCurrentShopId();

    $conversations = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->where("dispatch_requests.shop_id", $shopId)
      ->select(
        "dispatch_requests.id as dispatch_id",
        "dispatch_requests.issue_type",
        "dispatch_requests.status",
        "dispatch_requests.guest_name",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        ),
        DB::raw(
          '(SELECT COUNT(*) FROM messages WHERE messages.dispatch_id = dispatch_requests.id AND messages.conversation_type = "motorist" AND messages.sender_type = "motorist" AND messages.is_read = 0) as unread_count'
        )
      )
      ->latest("dispatch_requests.updated_at")
      ->get();

    $mechanicConversations = DB::table("dispatch_requests")
      ->leftJoin("dispatch_mechanics", "dispatch_requests.id", "=", "dispatch_mechanics.dispatch_request_id")
      ->leftJoin("users as mechanic", "dispatch_mechanics.mechanic_id", "=", "mechanic.id")
      ->where("dispatch_requests.shop_id", $shopId)
      ->whereNotNull("dispatch_mechanics.mechanic_id")
      ->select(
        "dispatch_requests.id as dispatch_id",
        "dispatch_requests.issue_type",
        "dispatch_requests.status",
        "dispatch_requests.guest_name",
        DB::raw(
          'COALESCE(mechanic.name, "Mechanic") as mechanic_name'
        ),
        DB::raw(
          'COALESCE(dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        ),
        DB::raw(
          '(SELECT COUNT(*) FROM messages WHERE messages.dispatch_id = dispatch_requests.id AND messages.conversation_type = "shop" AND messages.sender_type = "mechanic" AND messages.is_read = 0) as unread_count'
        )
      )
      ->latest("dispatch_requests.updated_at")
      ->get();

    $shopStatus = $this->getShopStatus();
    $shopStatusId = $this->getCurrentShopId()
      ? (int) optional($this->getShop())->status_id
      : null;

    return view(
      "shop.messages",
      compact("conversations", "mechanicConversations", "shopStatus", "shopStatusId")
    );
  }

  public function reviews()
  {
    $shopId = $this->getCurrentShopId();

    $reviews = DB::table("reviews")
      ->leftJoin("users", "reviews.motorist_id", "=", "users.id")
      ->leftJoin(
        "dispatch_requests",
        "reviews.dispatch_id",
        "=",
        "dispatch_requests.id"
      )
      ->where("reviews.shop_id", $shopId)
      ->select(
        "reviews.id",
        "reviews.rating",
        "reviews.comment",
        "reviews.created_at",
        "dispatch_requests.issue_type",
        "dispatch_requests.request_type",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Guest Motorist") as motorist_name'
        )
      )
      ->orderBy("reviews.created_at", "desc")
      ->get();

    // ✅ safe calculations (no crash if empty)
    $totalReviews = $reviews->count();
    $averageRating = $totalReviews > 0 ? round($reviews->avg("rating"), 1) : 0;

    $positivePercentage =
      $totalReviews > 0
        ? round(
          ($reviews->where("rating", ">=", 4)->count() / $totalReviews) * 100
        )
        : 0;

    $ratingBreakdown = [
      5 => $reviews->where("rating", 5)->count(),
      4 => $reviews->where("rating", 4)->count(),
      3 => $reviews->where("rating", 3)->count(),
      2 => $reviews->where("rating", 2)->count(),
      1 => $reviews->where("rating", 1)->count(),
    ];

    $shopStatus = $this->getShopStatus();
    $shopStatusId = (int) optional($this->getShop())->status_id ?: null;

    return view(
      "shop.reviews",
      compact(
        "reviews",
        "totalReviews",
        "averageRating",
        "positivePercentage",
        "ratingBreakdown",
        "shopStatus",
        "shopStatusId"
      )
    );
  }

  public function settings()
  {
    $shop = $this->getShop();
    $shopStatus = $this->getShopStatus();
    $shopStatusId = $shop ? (int) $shop->status_id : null;
    $shopStatuses = DB::table("shop_statuses")->orderBy("sort_order")->get();

    return view(
      "shop.settings",
      compact("shop", "shopStatus", "shopStatusId", "shopStatuses")
    );
  }

  public function toggleStatus()
  {
    $shopId = $this->getCurrentShopId();

    $shop = DB::table("shops")->where("id", $shopId)->first();

    if (!$shop) {
      return response()->json(
        ["success" => false, "message" => "Shop not found."],
        404
      );
    }

    $currentStatus = DB::table("shop_statuses")
      ->where("id", $shop->status_id)
      ->first();

    if (!$currentStatus || !$currentStatus->toggles_to_id) {
      return response()->json(
        ["success" => false, "message" => "Cannot toggle this status."],
        422
      );
    }

    $newStatusId = $currentStatus->toggles_to_id;

    DB::table("shops")
      ->where("id", $shopId)
      ->update(["status_id" => $newStatusId, "updated_at" => now()]);

    $newStatus = DB::table("shop_statuses")->where("id", $newStatusId)->first();

    broadcast(new ShopStatusUpdated($shopId, $newStatus->slug));

    return response()->json([
      "success" => true,
      "status" => $newStatus->slug,
      "status_id" => $newStatusId,
    ]);
  }

  public function getStatus()
  {
    return response()->json([
      "success" => true,
      "status" => $this->getShopStatus(),
    ]);
  }

  public function getMessages(int $dispatchId)
  {
    $shopId = $this->getCurrentShopId();
    $dispatch = DB::table('dispatch_requests')->where('id', $dispatchId)->firstOrFail();
    if ($dispatch->shop_id !== $shopId) {
      abort(403);
    }

    $conversationType = request()->query('conversation_type');
    if ($conversationType === 'mechanic') {
      $conversationType = 'shop';
    }

    if (in_array($conversationType, ['shop', 'motorist'], true)) {
      $markSender = $conversationType === 'motorist' ? 'motorist' : 'mechanic';
      DB::table('messages')
        ->where('dispatch_id', $dispatchId)
        ->where('conversation_type', $conversationType)
        ->where('sender_type', $markSender)
        ->where('is_read', false)
        ->update(['is_read' => true]);
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

    if (in_array($conversationType, ['shop', 'motorist'], true)) {
      $query->where('conversation_type', $conversationType);
      if ($conversationType === 'motorist') {
        // Motorist conversations should not include mechanic->motorist messages.
        $query->where('messages.sender_type', '!=', 'mechanic');
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
    $request->validate([
      "dispatch_id" => "required|exists:dispatch_requests,id",
      "message" => "required|string|max:1000",
      "conversation_type" => "nullable|in:shop,motorist,mechanic",
    ]);

    $shopId = $this->getCurrentShopId();
    $dispatch = DB::table('dispatch_requests')->where('id', $request->dispatch_id)->firstOrFail();
    if ($dispatch->shop_id !== $shopId) {
      abort(403);
    }

    $shopName = optional($this->getShop())->shop_name ?? 'Shop';
    $conversationType = $request->conversation_type ?? 'shop';
    if ($conversationType === 'mechanic') {
      $conversationType = 'shop';
    }

    $messageId = DB::table("messages")->insertGetId([
      "dispatch_id" => $request->dispatch_id,
      "shop_id" => $shopId,
      "sender_type" => "shop",
      "conversation_type" => $conversationType,
      "message" => $request->message,
      "created_at" => now(),
      "updated_at" => now(),
    ]);

    $messageData = [
      "id" => $messageId,
      "dispatch_id" => $request->dispatch_id,
      "shop_id" => $shopId,
      "sender_type" => "shop",
      "conversation_type" => $conversationType,
      "sender_name" => $shopName,
      "message" => $request->message,
      "created_at" => now()->toIso8601String(),
    ];

    broadcast(new DispatchMessageSent($request->dispatch_id, $messageData));

    return response()->json([
      "success" => true,
      "data" => $messageData,
    ]);
  }

  public function jobs()
  {
    $shopId = $this->getCurrentShopId();

    $jobs = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin(
        "guest_profiles as gp",
        "dispatch_requests.guest_token",
        "=",
        "gp.guest_token"
      )
      ->where("dispatch_requests.shop_id", $shopId)
      ->whereIn("dispatch_requests.status", [
        "accepted",
        "en_route",
        "arrived",
        "in_progress",
        "completed",
      ])
      ->select(
        "dispatch_requests.*",
        "gp.owner_name as owner_name",
        "gp.contact_number as contact_number",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        )
      )
      ->latest("dispatch_requests.created_at")
      ->get();

    $activeCount = DB::table("dispatch_requests")
      ->where("shop_id", $shopId)
      ->whereIn("status", ["accepted", "en_route", "arrived", "in_progress"])
      ->count();

    $completedToday = DB::table("dispatch_requests")
      ->where("shop_id", $shopId)
      ->where("status", "completed")
      ->whereDate("completed_at", now()->toDateString())
      ->count();

    $totalJobs = DB::table("dispatch_requests")
      ->where("shop_id", $shopId)
      ->where("status", "completed")
      ->count();

    $totalEarnings =
      DB::table("dispatch_requests")
        ->where("shop_id", $shopId)
        ->where("status", "completed")
        ->sum("price") ?? 0;

    $shopStatus = $this->getShopStatus();

    return view(
      "shop.jobs",
      compact(
        "jobs",
        "activeCount",
        "completedToday",
        "totalJobs",
        "totalEarnings",
        "shopStatus"
      )
    );
  }

  // -------------------------------------------------------------------------
  // MECHANIC MANAGEMENT
  // -------------------------------------------------------------------------

  public function mechanics()
  {
    $shopId = $this->getCurrentShopId();
    $mechanics = MechanicProfile::with("user")
      ->where("shop_id", $shopId)
      ->get();

    return view("shop.mechanics", compact("mechanics"));
  }

  public function storeMechanic(Request $request)
  {
    $request->validate([
      "name" => ["required", "string", "max:255"],
      "email" => ["required", "email", "max:255", "unique:users,email"],
      "password" => ["required", "string", "min:6"],
      "plate_number" => ["nullable", "string", "max:50"],
      "phone" => ["nullable", "string", "max:20"],
    ]);

    $shopId = $this->getCurrentShopId();

    $mechanic = User::create([
      "name" => $request->name,
      "email" => strtolower(trim($request->email)),
      "password" => Hash::make($request->password),
      "shop_id" => $shopId,
      "role" => User::ROLE_MECHANIC,
    ]);

    MechanicProfile::create([
      "user_id" => $mechanic->id,
      "shop_id" => $shopId,
      "plate_number" => $request->plate_number,
      "phone" => $request->phone,
      "status" => "available",
    ]);

    return redirect()
      ->route("shop.mechanics")
      ->with("success", "Mechanic added successfully.");
  }

  public function deleteMechanic(int $id)
  {
    $shopId = $this->getCurrentShopId();
    $profile = MechanicProfile::where("id", $id)
      ->where("shop_id", $shopId)
      ->firstOrFail();

    $userId = $profile->user_id;
    $profile->delete();
    User::where("id", $userId)->delete();

    return redirect()
      ->route("shop.mechanics")
      ->with("success", "Mechanic removed.");
  }

  public function dispatchMechanic(Request $request, int $id)
  {
    $request->validate([
      "mechanic_id" => ["required", "integer", "exists:users,id"],
    ]);

    $shopId = $this->getCurrentShopId();

    // Verify dispatch request belongs to this shop
    $dispatchExists = DB::table("dispatch_requests")
      ->where("id", $id)
      ->where("shop_id", $shopId)
      ->exists();

    if (!$dispatchExists) {
      return response()->json(
        ["success" => false, "message" => "Request not found."],
        404
      );
    }

    // Verify the mechanic belongs to this shop
    $profile = MechanicProfile::where("user_id", $request->mechanic_id)
      ->where("shop_id", $shopId)
      ->firstOrFail();

    // Check if mechanic is already dispatched to another request
    $existingDispatch = DispatchMechanic::where("mechanic_id", $request->mechanic_id)
      ->whereHas('dispatchRequest', function ($q) {
        $q->whereNotIn('status', ['completed', 'declined']);
      })
      ->first();

    if ($existingDispatch && $existingDispatch->dispatch_request_id != $id) {
      return response()->json(
        ["success" => false, "message" => "Mechanic is already assigned to another active request."],
        422
      );
    }

    // If there's an existing assignment for THIS request, free the old mechanic first
    $existing = DispatchMechanic::where("dispatch_request_id", $id)->first();
    if ($existing && $existing->mechanic_id != $request->mechanic_id) {
      MechanicProfile::where("user_id", $existing->mechanic_id)
        ->where("shop_id", $shopId)
        ->update(["status" => "available"]);
    }

    DispatchMechanic::updateOrCreate(
      ["dispatch_request_id" => $id],
      ["mechanic_id" => $request->mechanic_id, "status" => "assigned"]
    );

    $profile->update(["status" => "dispatched"]);

    $mechanicName =
      DB::table("users")->where("id", $request->mechanic_id)->value("name") ??
      "Mechanic";

    broadcast(
      new DispatchStatusUpdated(
        $id,
        DB::table("dispatch_requests")->where("id", $id)->value("status") ?? "accepted",
        $profile->shop_id
      )
    );

    return response()->json([
      "success" => true,
      "message" => "Mechanic assigned.",
      "mechanic_name" => $mechanicName,
    ]);
  }

  public function mechanicsList()
  {
    $shopId = $this->getCurrentShopId();
    $mechanics = DB::table("mechanic_profiles")
      ->join("users", "users.id", "=", "mechanic_profiles.user_id")
      ->where("mechanic_profiles.shop_id", $shopId)
      ->select(
        "users.id as user_id",
        "users.name",
        "mechanic_profiles.status",
        "mechanic_profiles.phone"
      )
      ->get();

    return response()->json($mechanics);
  }
}
