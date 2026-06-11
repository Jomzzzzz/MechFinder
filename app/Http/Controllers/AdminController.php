<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
  public function dashboard()
  {
    $totalShops = DB::table("shops")->count();
    $totalUsers = DB::table("users")->where("role", "shop")->count();
    $totalRequests = DB::table("dispatch_requests")->count();
    $totalReviews = DB::table("reviews")->count();

    $recentRequests = DB::table("dispatch_requests")
      ->leftJoin("shops", "dispatch_requests.shop_id", "=", "shops.id")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->select(
        "dispatch_requests.id",
        "dispatch_requests.status",
        "dispatch_requests.issue_type",
        "dispatch_requests.created_at",
        "shops.shop_name",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Guest") as motorist_name'
        )
      )
      ->latest("dispatch_requests.created_at")
      ->limit(20)
      ->get();

    $shops = DB::table("shops")
      ->leftJoin("users", "shops.owner_id", "=", "users.id")
      ->select(
        "shops.*",
        "users.name as owner_name",
        "users.email as owner_email"
      )
      ->get();

    return view(
      "admin.dashboard",
      compact(
        "totalShops",
        "totalUsers",
        "totalRequests",
        "totalReviews",
        "recentRequests",
        "shops"
      )
    );
  }

  public function users()
  {
    $users = DB::table("users")
      ->leftJoin("shops", "users.shop_id", "=", "shops.id")
      ->select("users.*", "shops.shop_name")
      ->orderBy("users.created_at", "desc")
      ->get();

    return view("admin.users", compact("users"));
  }

  public function shops()
  {
    $shops = DB::table("shops")
      ->join("shop_statuses", "shops.status_id", "=", "shop_statuses.id")
      ->leftJoin("users", "shops.owner_id", "=", "users.id")
      ->leftJoin("reviews", "shops.id", "=", "reviews.shop_id")
      ->select(
        "shops.*",
        "shop_statuses.slug as status",
        "shop_statuses.label as status_label",
        "users.name as owner_name",
        "users.email as owner_email",
        DB::raw("COALESCE(AVG(reviews.rating), 0) as avg_rating"),
        DB::raw("COUNT(reviews.id) as review_count")
      )
      ->groupBy(
        "shops.id",
        "shops.owner_id",
        "shops.shop_name",
        "shops.address",
        "shops.phone",
        "shops.email",
        "shops.latitude",
        "shops.longitude",
        "shops.location",
        "shops.status_id",
        "shops.created_at",
        "shops.updated_at",
        "shop_statuses.slug",
        "shop_statuses.label",
        "users.name",
        "users.email"
      )
      ->get();

    return view("admin.shops", compact("shops"));
  }

  public function createShop()
  {
    $shopStatuses = DB::table("shop_statuses")->orderBy("label")->get();
    return view("admin.shops.create", compact("shopStatuses"));
  }

  public function storeShop(Request $request)
  {
    $validated = $request->validate([
      "shop_name" => "required|string|max:255",
      "address" => "required|string|max:255",
      "phone" => "nullable|string|max:50",
      "email" => ["nullable", "email", "max:255", Rule::unique("shops", "email")],
      "latitude" => "required|numeric|between:-90,90",
      "longitude" => "required|numeric|between:-180,180",
      "status_id" => "required|exists:shop_statuses,id",
    ]);

    DB::table("shops")->insert([
      "shop_name" => $validated["shop_name"],
      "address" => $validated["address"],
      "phone" => $validated["phone"],
      "email" => $validated["email"],
      "latitude" => $validated["latitude"],
      "longitude" => $validated["longitude"],
      "location" => $validated["address"],
      "status_id" => $validated["status_id"],
      "created_at" => now(),
      "updated_at" => now(),
    ]);

    return redirect()->route("admin.shops")->with("success", "Shop created successfully.");
  }

  public function editShop(int $id)
  {
    $shop = DB::table("shops")->find($id);
    if (!$shop) {
      return back()->with("error", "Shop not found.");
    }

    $shopStatuses = DB::table("shop_statuses")->orderBy("label")->get();
    return view("admin.shops.edit", compact("shop", "shopStatuses"));
  }

  public function updateShop(Request $request, int $id)
  {
    $shop = DB::table("shops")->find($id);
    if (!$shop) {
      return back()->with("error", "Shop not found.");
    }

    $validated = $request->validate([
      "shop_name" => "required|string|max:255",
      "address" => "required|string|max:255",
      "phone" => "nullable|string|max:50",
      "email" => [
        "nullable",
        "email",
        "max:255",
        Rule::unique("shops", "email")->ignore($id),
      ],
      "latitude" => "required|numeric|between:-90,90",
      "longitude" => "required|numeric|between:-180,180",
      "status_id" => "required|exists:shop_statuses,id",
    ]);

    DB::table("shops")
      ->where("id", $id)
      ->update([
        "shop_name" => $validated["shop_name"],
        "address" => $validated["address"],
        "phone" => $validated["phone"],
        "email" => $validated["email"],
        "latitude" => $validated["latitude"],
        "longitude" => $validated["longitude"],
        "location" => $validated["address"],
        "status_id" => $validated["status_id"],
        "updated_at" => now(),
      ]);

    return redirect()->route("admin.shops")->with("success", "Shop updated successfully.");
  }

  public function geocodeAddress(Request $request)
  {
    $request->validate([
      "address" => "required|string|max:500",
    ]);

    $address = trim($request->address);
    $apiKey = env('GOOGLE_MAPS_API_KEY');

    if ($apiKey) {
      $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
        'address' => $address,
        'key' => $apiKey,
      ]);

      if (!$response->successful()) {
        return response()->json(['error' => 'Geocoding service unavailable.'], 500);
      }

      $result = $response->json();
      if (isset($result['status']) && $result['status'] === 'OK' && !empty($result['results'])) {
        $location = $result['results'][0]['geometry']['location'];
        return response()->json([
          'lat' => $location['lat'],
          'lng' => $location['lng'],
          'formatted_address' => $result['results'][0]['formatted_address'],
        ]);
      }

      return response()->json(['error' => $result['error_message'] ?? 'Address not found.'], 422);
    }

    // Fallback to OpenStreetMap Nominatim if Google API key is not configured.
    $response = Http::get('https://nominatim.openstreetmap.org/search', [
      'q' => $address,
      'format' => 'json',
      'limit' => 1,
    ]);

    if (!$response->successful()) {
      return response()->json(['error' => 'Geocoding service unavailable.'], 500);
    }

    $result = $response->json();
    if (!empty($result) && isset($result[0])) {
      return response()->json([
        'lat' => (float) $result[0]['lat'],
        'lng' => (float) $result[0]['lon'],
        'formatted_address' => $result[0]['display_name'],
      ]);
    }

    return response()->json(['error' => 'Address not found.'], 422);
  }

  public function requests(Request $request)
  {
    $status = $request->query("status");

    $query = DB::table("dispatch_requests")
      ->leftJoin("shops", "dispatch_requests.shop_id", "=", "shops.id")
      ->leftJoin("users", "dispatch_requests.motorist_id", "=", "users.id")
      ->select(
        "dispatch_requests.*",
        "shops.shop_name",
        DB::raw(
          'COALESCE(users.name, dispatch_requests.guest_name, "Guest") as motorist_name'
        )
      );

    if ($status) {
      $query->where("dispatch_requests.status", $status);
    }

    $data = $query->latest("dispatch_requests.created_at")->get();

    return view("admin.requests", compact("data", "status"));
  }

  public function updateUserRole(Request $request, int $id)
  {
    $request->validate([
      "role" => "required|in:admin,shop,user",
    ]);

    DB::table("users")
      ->where("id", $id)
      ->update([
        "role" => $request->role,
        "updated_at" => now(),
      ]);

    return back()->with("success", "User role updated.");
  }

  public function deleteShop(int $id)
  {
    DB::table("shops")->where("id", $id)->delete();

    return back()->with("success", "Shop deleted.");
  }

  public function maps()
  {
    $maps = DB::table("map_locations")
      ->orderBy("created_at", "desc")
      ->get();

    return view("admin.maps.index", compact("maps"));
  }

  public function createMap()
  {
    return view("admin.maps.create");
  }

  public function storeMap(Request $request)
  {
    $validated = $request->validate([
      "title" => "required|string|max:255",
      "address" => "nullable|string|max:255",
      "description" => "nullable|string",
      "latitude" => "required|numeric|between:-90,90",
      "longitude" => "required|numeric|between:-180,180",
    ]);

    DB::table("map_locations")->insert(array_merge($validated, [
      "created_at" => now(),
      "updated_at" => now(),
    ]));

    return redirect()->route("admin.maps")->with("success", "Map location added.");
  }

  public function editMap(int $id)
  {
    $map = DB::table("map_locations")->find($id);

    if (!$map) {
      return back()->with("error", "Map location not found.");
    }

    return view("admin.maps.edit", compact("map"));
  }

  public function updateMap(Request $request, int $id)
  {
    $validated = $request->validate([
      "title" => "required|string|max:255",
      "address" => "nullable|string|max:255",
      "description" => "nullable|string",
      "latitude" => "required|numeric|between:-90,90",
      "longitude" => "required|numeric|between:-180,180",
    ]);

    $updated = DB::table("map_locations")
      ->where("id", $id)
      ->update(array_merge($validated, ["updated_at" => now()]));

    if (!$updated) {
      return back()->with("error", "Map location not found.");
    }

    return redirect()->route("admin.maps")->with("success", "Map location updated.");
  }

  public function deleteMap(int $id)
  {
    DB::table("map_locations")->where("id", $id)->delete();

    return back()->with("success", "Map location deleted.");
  }
}
