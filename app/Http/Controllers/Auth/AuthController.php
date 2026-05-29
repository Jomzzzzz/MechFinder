<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function showLogin()
  {
    if (Auth::check()) {
      return redirect($this->redirectByRole(Auth::user()->role));
    }

    return view("auth.login");
  }

  // Motorist signup
  public function showSignup()
  {
    if (Auth::check()) {
      return redirect($this->redirectByRole(Auth::user()->role));
    }

    return view("auth.signup-motorist");
  }

  public function registerMotorist(Request $request)
  {
    $request->validate([
      "name" => ["required", "string", "max:255"],
      "email" => ["required", "email", "max:255", "unique:users,email"],
      "password" => ["required", "string", "min:6", "confirmed"],
    ]);

    User::create([
      "name" => $request->name,
      "email" => strtolower(trim($request->email)),
      "password" => Hash::make($request->password),
      "role" => User::ROLE_MOTORIST,
    ]);

    return redirect()
      ->route("login")
      ->with("success", "Registration successful. Please log in.");
  }

  // Shop signup
  public function showShopSignup()
  {
    if (Auth::check()) {
      return redirect($this->redirectByRole(Auth::user()->role));
    }

    return view("auth.signup-shop");
  }

  public function registerShop(Request $request)
  {
    $request->validate([
      "name" => ["required", "string", "max:255"],
      "email" => ["required", "email", "max:255", "unique:users,email"],
      "password" => ["required", "string", "min:6", "confirmed"],
      "shop_name" => ["required", "string", "max:255"],
      "address" => ["required", "string", "max:255"],
      "phone" => ["nullable", "string", "max:20"],
    ]);

    $email = strtolower(trim($request->email));

    $shop = Shop::create([
      "shop_name" => $request->shop_name,
      "address" => $request->address,
      "phone" => $request->phone,
      "email" => $email,
      "status" => "closed",
    ]);

    $user = User::create([
      "name" => $request->name,
      "email" => $email,
      "password" => Hash::make($request->password),
      "shop_id" => $shop->id,
      "role" => User::ROLE_SHOP,
    ]);

    $shop->update(["owner_id" => $user->id]);

    return redirect()
      ->route("login")
      ->with("success", "Shop registered successfully. Please log in.");
  }

  public function manualLogin(Request $request)
  {
    $request->validate([
      "email" => ["required", "email", "max:255"],
      "password" => ["required", "string", "min:6"],
    ]);

    $email = strtolower(trim($request->email));
    $user = User::query()->where("email", $email)->first();

    if (!$user) {
      return back()
        ->withInput()
        ->with("error", "This email is not registered. Please sign up first.");
    }

    if (!Hash::check($request->password, $user->password)) {
      return back()->withInput()->with("error", "Incorrect password.");
    }

    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    return redirect($this->redirectByRole($user->role));
  }

  private function redirectByRole(string $role): string
  {
    return match ($role) {
      User::ROLE_ADMIN => "/admin/dashboard",
      User::ROLE_SHOP => "/shop/dashboard",
      User::ROLE_MECHANIC => "/mechanic/dashboard",
      default => "/motorist",
    };
  }

  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect("/")->with("success", "You have been logged out.");
  }
}
