<?php

namespace App\Http\Controllers;

use App\Events\DispatchStatusUpdated;
use App\Events\ShopStatusUpdated;
use App\Models\DispatchMechanic;
use App\Models\DispatchRequest;
use App\Models\MechanicProfile;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MechanicController extends Controller
{
  public function dashboard()
  {
    $mechanic = Auth::user();

    $jobs = DispatchMechanic::with([
      "dispatchRequest.shop",
      "dispatchRequest.motorist",
    ])
      ->where("mechanic_id", $mechanic->id)
      ->latest()
      ->get();

    $profile = $mechanic->mechanicProfile;

    return view("mechanic.dashboard-mobile", compact("jobs", "profile", "mechanic"));
  }

  public function profile()
  {
    $mechanic = Auth::user();
    $profile = $mechanic->mechanicProfile;

    return view("mechanic.profile-mobile", compact("mechanic", "profile"));
  }

  public function maps()
  {
    $mechanic = Auth::user();

    $jobs = DispatchMechanic::with([
      "dispatchRequest.shop",
      "dispatchRequest.motorist",
    ])
      ->where("mechanic_id", $mechanic->id)
      ->latest()
      ->get();

    return view("mechanic.maps-mobile", compact("jobs", "mechanic"));
  }

  public function updateProfile(Request $request)
  {
    $request->validate([
      "phone" => [
        "nullable",
        "string",
        "size:11",
        "regex:/^0[0-9]{10}$/",
      ],
      "plate_number" => [
        "nullable",
        "string",
        "min:2",
        "max:15",
        "regex:/^[A-Za-z0-9 ]+$/",
      ],
    ], [
      "phone.size" => "Contact number must be exactly 11 digits.",
      "phone.regex" => "Contact number must start with 0 and contain only numbers.",
      "plate_number.regex" => "Plate number may contain only letters, numbers, and spaces.",
      "plate_number.min" => "Plate number must be at least 2 characters.",
      "plate_number.max" => "Plate number cannot exceed 15 characters.",
    ]);

    $mechanic = Auth::user();
    $profile = $mechanic->mechanicProfile;

    if ($profile) {
      $profile->update($request->only([
        "phone",
        "plate_number",
      ]));
    }

    return redirect()
      ->route("mechanic.profile")
      ->with("success", "Profile updated successfully.");
  }

  public function changePassword(Request $request)
  {
    $request->validate([
      "current_password" => ["required"],
      "password" => [
        "required",
        "string",
        "min:12",
        "confirmed",
        "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/",
      ],
    ], [
      "password.min" => "Password must be at least 12 characters.",
      "password.regex" => "Password must include uppercase and lowercase letters, numbers, and special characters.",
    ]);

    $mechanic = Auth::user();

    if (!Hash::check($request->current_password, $mechanic->password)) {
      return back()->withErrors([
        "current_password" => "Current password is incorrect.",
      ])->withInput();
    }

    if (Hash::check($request->password, $mechanic->password)) {
      return back()->withErrors([
        "password" => "New password must be different from the current password.",
      ])->withInput();
    }

    $mechanic->update(["password" => Hash::make($request->password)]);

    return redirect()
      ->route("mechanic.profile")
      ->with("pw_success", "Password updated successfully.");
  }

  public function updateRequestStatus(Request $request, int $id)
  {
    $validated = $request->validate([
      "status" => ["required", "in:en_route,arrived,completed"],
    ]);

    $mechanic = Auth::user();
    $job = DispatchMechanic::where("dispatch_request_id", $id)
      ->where("mechanic_id", $mechanic->id)
      ->firstOrFail();

    $dispatch = $job->dispatchRequest;
    if (!$dispatch) {
      return response()->json(["success" => false, "message" => "Dispatch request not found."], 404);
    }

    $allowedTransitions = [
      "en_route" => ["accepted"],
      "arrived" => ["en_route"],
      "completed" => ["arrived"],
    ];

    if (!in_array($dispatch->status, $allowedTransitions[$validated["status"]] ?? [])) {
      return response()->json(
        [
          "success" => false,
          "message" => "Unable to change status from {$dispatch->status} to {$validated['status']}.",
        ],
        422
      );
    }

    $updateData = ["status" => $validated["status"], "updated_at" => now()];
    if ($validated["status"] === "en_route") {
      $updateData["en_route_at"] = now();
    }

    if ($validated["status"] === "arrived") {
      $updateData["arrived_at"] = now();
    }

    if ($validated["status"] === "completed") {
      $updateData["completed_at"] = now();
    }

    DispatchRequest::where("id", $dispatch->id)->update($updateData);
    $job->update(["status" => $validated["status"]]);

    if (in_array($validated["status"], ["en_route", "arrived"], true) && $dispatch->shop_id) {
      $busyStatusId = DB::table("shop_statuses")
        ->where("slug", "busy")
        ->value("id");

      if ($busyStatusId) {
        Shop::where("id", $dispatch->shop_id)->update(["status_id" => $busyStatusId]);
        broadcast(new ShopStatusUpdated($dispatch->shop_id, "busy"));
      }
    }

    if ($validated["status"] === "completed") {
      DispatchMechanic::where("dispatch_request_id", $dispatch->id)
        ->update(["status" => "completed"]);

      $profile = $mechanic->mechanicProfile;
      if ($profile && $profile->status !== "available") {
        $profile->update(["status" => "available"]);
      }

      if ($dispatch->shop_id) {
        $openStatusId = DB::table("shop_statuses")
          ->where("slug", "open")
          ->value("id");

        if ($openStatusId) {
          Shop::where("id", $dispatch->shop_id)->update(["status_id" => $openStatusId]);
          broadcast(new ShopStatusUpdated($dispatch->shop_id, "open"));
        }
      }
    }

    broadcast(new DispatchStatusUpdated(
      $dispatch->id,
      $validated["status"],
      $dispatch->shop_id
    ));

    return response()->json([
      "success" => true,
      "status" => $validated["status"],
    ]);
  }
}
