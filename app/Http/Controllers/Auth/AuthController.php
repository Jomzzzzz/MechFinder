<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/shop/dashboard');
        }

        return view('auth.login');
    }

    public function showSignup()
    {
        if (Auth::check()) {
            return redirect('/shop/dashboard');
        }

        return view('auth.signup');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $email = strtolower(trim($request->email));

        if (!str_ends_with($email, '@gmail.com')) {
            return back()->withInput()->with('error', 'Only Gmail accounts are allowed.');
        }

        $shopId = $this->createShopByEmail($email);

        User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'shop_id' => $shopId,
            'role' => 'shop',
            'registered_via_google' => false,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Successful sign up, go to login.');
    }

    public function manualLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $email = strtolower(trim($request->email));

        if (!str_ends_with($email, '@gmail.com')) {
            return back()->withInput()->with('error', 'Only Gmail accounts are allowed.');
        }

        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            return back()->withInput()->with('error', 'This Gmail is not registered. Please sign up first.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withInput()->with('error', 'Incorrect password.');
        }

        if (!$user->shop_id) {
            $user->shop_id = $this->createShopByEmail($email);
            $user->role = 'shop';
            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect('/shop/dashboard');
    }

    public function redirectToGoogleLogin()
    {
        session(['google_auth_mode' => 'login']);

        return Socialite::driver('google')->redirect();
    }

    public function redirectToGoogleSignup()
    {
        session(['google_auth_mode' => 'signup']);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = strtolower(trim($googleUser->getEmail()));
            $mode = session('google_auth_mode', 'login');

            if (!str_ends_with($email, '@gmail.com')) {
                return redirect()->route('login')->with('error', 'Only Gmail accounts are allowed.');
            }

            $existingUser = User::query()->where('email', $email)->first();

            if ($mode === 'login') {
                if (!$existingUser) {
                    return redirect()->route('signup')->with('error', 'This Gmail is not registered. Please sign up first.');
                }

                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token ?? null,
                    'google_refresh_token' => $googleUser->refreshToken ?? $existingUser->google_refresh_token,
                    'registered_via_google' => true,
                ]);

                Auth::login($existingUser, true);
                $request->session()->regenerate();

                return redirect('/shop/dashboard');
            }

            if ($existingUser) {
                return redirect()->route('login')->with('success', 'This Gmail is already registered. Please login.');
            }

            $shopId = $this->createShopByEmail($email);

            User::create([
                'name' => $googleUser->getName() ?: explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make($googleUser->getId() . $email),
                'shop_id' => $shopId,
                'role' => 'shop',
                'registered_via_google' => true,
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token ?? null,
                'google_refresh_token' => $googleUser->refreshToken ?? null,
            ]);

            return redirect()
                ->route('login')
                ->with('success', 'Successful sign up, go to login.');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }
    }

    private function createShopByEmail(string $email): int
    {
        $email = strtolower(trim($email));

        if (Schema::hasColumn('shops', 'email')) {
            $existingShop = DB::table('shops')->where('email', $email)->first();

            if ($existingShop) {
                return (int) $existingShop->id;
            }
        }

        $data = [
            'shop_name' => explode('@', $email)[0] . "'s Shop",
            'phone' => null,
            'location' => 'Olongapo City',
            'address' => 'Olongapo City',
            'latitude' => 14.8386,
            'longitude' => 120.2842,
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('shops', 'email')) {
            $data['email'] = $email;
        }

        if (Schema::hasColumn('shops', 'rating')) {
            $data['rating'] = 5.0;
        }

        return (int) DB::table('shops')->insertGetId($data);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }
}