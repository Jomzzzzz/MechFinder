<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
      ->leftJoin("users", "shops.owner_id", "=", "users.id")
      ->leftJoin("reviews", "shops.id", "=", "reviews.shop_id")
      ->select(
        "shops.*",
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
        "shops.status",
        "shops.created_at",
        "shops.updated_at",
        "users.name",
        "users.email"
      )
      ->get();

    return view("admin.shops", compact("shops"));
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
}
