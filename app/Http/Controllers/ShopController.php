<?php

namespace App\Http\Controllers;

use App\Events\DispatchStatusUpdated;
use App\Models\MechanicProfile;
use App\Models\DispatchMechanic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
    return DB::table("shops")->where("id", $this->getCurrentShopId())->first();
  }

  protected function getShopStatus()
  {
    $shop = $this->getShop();

    return $shop ? strtolower($shop->status) : "closed";
  }

  public function dashboard()
  {
    $shopId = $this->getCurrentShopId();

    $requests = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin("guest_profiles as gp", "dispatch_requests.guest_token", "=", "gp.guest_token")
      ->where("dispatch_requests.shop_id", $shopId)
      ->where("dispatch_requests.status", "requested")
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

    $jobs = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin("guest_profiles as gp", "dispatch_requests.guest_token", "=", "gp.guest_token")
      ->where("dispatch_requests.shop_id", $shopId)
      ->whereIn("dispatch_requests.status", [
        "accepted",
        "en_route",
        "arrived",
        "in_progress",
      ])
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
      ->whereIn("status", ["accepted", "en_route", "arrived", "in_progress"])
      ->count();

    $shop = $this->getShop();
    $shopStatus = $this->getShopStatus();

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
        "needsProfileCompletion"
      )
    );
  }

  public function fetchRequests()
  {
    $shopId = $this->getCurrentShopId();

    $requests = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin("guest_profiles as gp", "dispatch_requests.guest_token", "=", "gp.guest_token")
      ->where("dispatch_requests.shop_id", $shopId)
      ->where("dispatch_requests.status", "requested")
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

    $html = view("components.requests-list", compact("requests"))->render();

    return response()->json([
      "success" => true,
      "html" => $html,
      "pending" => $requests->count(),
      "shopStatus" => $this->getShopStatus(),
    ]);
  }

  public function requests(Request $request)
  {
    $shopId = $this->getCurrentShopId();

    $status = $request->query("status");

    $query = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin("guest_profiles as gp", "dispatch_requests.guest_token", "=", "gp.guest_token")
      ->where("dispatch_requests.shop_id", $shopId)
      ->select(
        "dispatch_requests.*",
        "gp.owner_name as owner_name",
        "gp.contact_number as contact_number",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name'
        )
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
      ->leftJoin("guest_profiles as gp", "dispatch_requests.guest_token", "=", "gp.guest_token")
      ->where("dispatch_requests.shop_id", $shopId)
      ->whereIn("dispatch_requests.status", [
        "accepted",
        "en_route",
        "arrived",
        "in_progress",
      ])
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
      ->where("id", $shopId)
      ->select(
        "id",
        "shop_name",
        "address",
        "location",
        "latitude",
        "longitude",
        "status"
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
        "in_progress",
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
    DB::table("dispatch_requests")
      ->where("id", $id)
      ->where("shop_id", $this->getCurrentShopId())
      ->update([
        "status" => "accepted",
        "accepted_at" => now(),
        "updated_at" => now(),
      ]);

    broadcast(new DispatchStatusUpdated($id, "accepted"));

    return response()->json(["success" => true, "status" => "accepted"]);
  }

  public function decline(int $id)
  {
    $shopId = $this->getCurrentShopId();

    $req = DB::table("dispatch_requests")
      ->where("id", $id)
      ->where("shop_id", $shopId)
      ->where("status", "requested")
      ->first();

    if (!$req) {
      return response()->json(["success" => false, "message" => "Not authorized or request no longer pending."], 403);
    }

    // Try to find the next nearest open shop (skip the one that just declined)
    $lat        = $req->latitude  ? (float) $req->latitude  : null;
    $lng        = $req->longitude ? (float) $req->longitude : null;
    $nextShopId = $this->findNearestOpenShop($lat, $lng, [$shopId]);

    if ($nextShopId) {
      // Reassign to next shop — stays "requested"
      DB::table("dispatch_requests")
        ->where("id", $id)
        ->update(["shop_id" => $nextShopId, "updated_at" => now()]);

      $reqData = DB::table("dispatch_requests")
        ->leftJoin("guest_profiles as gp", "dispatch_requests.guest_token", "=", "gp.guest_token")
        ->where("dispatch_requests.id", $id)
        ->select("dispatch_requests.*", "gp.owner_name", "gp.contact_number")
        ->first();

      broadcast(new DispatchRequestCreated((int) $nextShopId, [
        "id"                    => $id,
        "issue_type"            => $reqData->issue_type,
        "owner_name"            => $reqData->owner_name ?? $reqData->guest_name ?? "Unknown",
        "contact_number"        => $reqData->contact_number ?? "",
        "vehicle_make_model"    => $reqData->vehicle_make_model ?? null,
        "vehicle_variant_color" => $reqData->vehicle_variant_color ?? null,
        "plate_temp_number"     => $reqData->plate_temp_number ?? null,
        "description"           => $reqData->description ?? null,
        "location"              => $reqData->location ?? null,
        "status"                => "requested",
        "created_at"            => $reqData->created_at,
      ]))->toOthers();
    } else {
      // No other open shops available — notify motorist
      DB::table("dispatch_requests")
        ->where("id", $id)
        ->update(["status" => "declined", "updated_at" => now()]);

      broadcast(new DispatchStatusUpdated($id, "declined"));
    }

    return response()->json(["success" => true]);
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
        return $this->distanceKm($lat, $lng, (float)$shop->latitude, (float)$shop->longitude);
      })
      ->first()?->id;
  }

  protected function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
  {
    $R    = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    return round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
  }

  public function updateRequestStatus(Request $request, int $id)
  {
    $validated = $request->validate([
      "status" =>
        "required|in:requested,accepted,en_route,arrived,in_progress,completed,declined",
    ]);

    $updateData = [
      "status" => $validated["status"],
      "updated_at" => now(),
    ];

    if ($validated["status"] === "accepted") {
      $updateData["accepted_at"] = now();
    }

    if ($validated["status"] === "en_route") {
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

    broadcast(new DispatchStatusUpdated($id, $validated["status"]));

    return response()->json([
      "success" => true,
      "status" => $validated["status"],
    ]);
  }

  public function uploadImages(Request $request)
  {
    $request->validate([
      "logo"        => "nullable|image|mimes:jpeg,jpg,png,webp|max:2048",
      "cover_photo" => "nullable|image|mimes:jpeg,jpg,png,webp|max:4096",
    ]);

    $shopId = $this->getCurrentShopId();
    $shop   = $this->getShop();
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
      $update["cover_photo"] = $request->file("cover_photo")->store("shops/covers", "public");
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
      "status" => "required|in:open,busy,closed,maintenance",
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
        "status" => $validated["status"],
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
        )
      )
      ->latest("dispatch_requests.updated_at")
      ->get();

    $shopStatus = $this->getShopStatus();

    return view("shop.messages", compact("conversations", "shopStatus"));
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

    return view(
      "shop.reviews",
      compact(
        "reviews",
        "totalReviews",
        "averageRating",
        "positivePercentage",
        "ratingBreakdown",
        "shopStatus"
      )
    );
  }

  public function settings()
  {
    $shop = $this->getShop();
    $shopStatus = $this->getShopStatus();

    return view("shop.settings", compact("shop", "shopStatus"));
  }

  public function toggleStatus()
  {
    $shopId = $this->getCurrentShopId();

    $shop = DB::table("shops")->where("id", $shopId)->first();

    if (!$shop) {
      return response()->json(
        [
          "success" => false,
          "message" => "Shop not found.",
        ],
        404
      );
    }

    $newStatus = $shop->status === "open" ? "closed" : "open";

    DB::table("shops")
      ->where("id", $shopId)
      ->update([
        "status" => $newStatus,
        "updated_at" => now(),
      ]);

    return response()->json([
      "success" => true,
      "status" => $newStatus,
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

    $messages = DB::table("messages")
      ->where("dispatch_id", $dispatchId)
      ->orderBy("created_at", "asc")
      ->get();

    return response()->json([
      "success" => true,
      "messages" => $messages,
    ]);
  }

  public function sendMessage(Request $request)
  {
    $request->validate([
      "dispatch_id" => "required",
      "message" => "required|string|max:1000",
    ]);

    DB::table("messages")->insert([
      "dispatch_id" => $request->dispatch_id,
      "shop_id" => $this->getCurrentShopId(),
      "sender_type" => "shop",
      "message" => $request->message,
      "created_at" => now(),
      "updated_at" => now(),
    ]);

    return response()->json([
      "success" => true,
    ]);
  }

  public function jobs()
  {
    $shopId = $this->getCurrentShopId();

    $jobs = DB::table("dispatch_requests")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->leftJoin("guest_profiles as gp", "dispatch_requests.guest_token", "=", "gp.guest_token")
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

    // Verify the mechanic belongs to this shop
    $profile = MechanicProfile::where("user_id", $request->mechanic_id)
      ->where("shop_id", $shopId)
      ->firstOrFail();

    DispatchMechanic::create([
      "dispatch_request_id" => $id,
      "mechanic_id" => $request->mechanic_id,
      "status" => "assigned",
    ]);

    $profile->update(["status" => "dispatched"]);

    return response()->json([
      "success" => true,
      "message" => "Mechanic dispatched.",
    ]);
  }
}
