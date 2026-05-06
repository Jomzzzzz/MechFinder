<?php

namespace App\Http\Controllers;

use App\Models\DispatchMechanic;
use App\Models\MechanicProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    return view("mechanic.dashboard", compact("jobs", "profile", "mechanic"));
  }

  public function profile()
  {
    $mechanic = Auth::user();
    $profile = $mechanic->mechanicProfile;

    return view("mechanic.profile", compact("mechanic", "profile"));
  }

  public function updateProfile(Request $request)
  {
    $request->validate([
      "plate_number" => ["nullable", "string", "max:50"],
      "phone" => ["nullable", "string", "max:20"],
    ]);

    $mechanic = Auth::user();
    $profile = $mechanic->mechanicProfile;

    if ($profile) {
      $profile->update([
        "plate_number" => $request->plate_number,
        "phone" => $request->phone,
      ]);
    }

    return redirect()
      ->route("mechanic.profile")
      ->with("success", "Profile updated.");
  }
}
