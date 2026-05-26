<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
  public function redirectToGoogle($type = 'motorist')
  {
    session(['oauth_type' => $type]);
    return Socialite::driver('google')->redirect();
  }

  public function handleGoogleCallback()
  {
    try {
      $googleUser = Socialite::driver('google')->user();
    } catch (\Exception $e) {
      return redirect('/login')->with('error', 'Failed to authenticate with Google');
    }

    $type = session('oauth_type', 'motorist');

    $user = User::where('email', $googleUser->getEmail())->first();

    if ($user) {
      Auth::login($user, remember: true);
      return $this->redirectBasedOnRole($user->role);
    }

    $user = User::create([
      'name' => $googleUser->getName(),
      'email' => $googleUser->getEmail(),
      'google_id' => $googleUser->getId(),
      'role' => $type === 'shop' ? 'shop' : 'motorist',
      'password' => bcrypt(\Str::random(24)),
    ]);

    if ($type === 'shop') {
      \DB::table('shops')->insert([
        'user_id' => $user->id,
        'shop_name' => $googleUser->getName(),
        'email' => $googleUser->getEmail(),
        'status' => 'pending',
        'latitude' => 0,
        'longitude' => 0,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    Auth::login($user, remember: true);
    return $this->redirectBasedOnRole($user->role);
  }

  private function redirectBasedOnRole($role)
  {
    return match ($role) {
      'shop' => redirect('/shop/settings')->with('success', 'Account created! Please complete your shop profile.'),
      'admin' => redirect('/admin/dashboard'),
      'mechanic' => redirect('/mechanic/dashboard'),
      default => redirect('/motorist/dashboard'),
    };
  }
}
