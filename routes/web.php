<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\MotoristController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MechanicController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get("/", function () {
  return view("welcome");
})->name("home");

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get("/login", [AuthController::class, "showLogin"])->name("login");
Route::post("/login", [AuthController::class, "manualLogin"])->name(
  "login.post"
);

// Motorist signup
Route::get("/signup", [AuthController::class, "showSignup"])->name("signup");
Route::post("/signup", [AuthController::class, "registerMotorist"])->name(
  "signup.post"
);

// Shop signup
Route::get("/signup/shop", [AuthController::class, "showShopSignup"])->name(
  "signup.shop"
);
Route::post("/signup/shop", [AuthController::class, "registerShop"])->name(
  "signup.shop.post"
);

Route::post("/logout", [AuthController::class, "logout"])->name("logout");

// Google OAuth
Route::get("/auth/google/login", [OAuthController::class, "redirectToGoogle"])->defaults('type', 'motorist')->name("auth.google.login");
Route::get("/auth/google/signup", [OAuthController::class, "redirectToGoogle"])->defaults('type', 'shop')->name("auth.google.signup");
Route::get("/auth/google/callback", [OAuthController::class, "handleGoogleCallback"])->name("auth.google.callback");

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES - role:admin only
|--------------------------------------------------------------------------
*/

Route::prefix("admin")
  ->middleware(["auth", "role:admin"])
  ->group(function () {
    Route::get("/", [AdminController::class, "dashboard"])->name(
      "admin.dashboard"
    );
    Route::get("/dashboard", [AdminController::class, "dashboard"]);
    Route::get("/users", [AdminController::class, "users"])->name(
      "admin.users"
    );
    Route::get("/shops", [AdminController::class, "shops"])->name(
      "admin.shops"
    );
    Route::get("/requests", [AdminController::class, "requests"])->name(
      "admin.requests"
    );
    Route::post("/users/{id}/role", [
      AdminController::class,
      "updateUserRole",
    ])->name("admin.users.role");
    Route::delete("/shops/{id}", [AdminController::class, "deleteShop"])->name(
      "admin.shops.delete"
    );
  });

/*
|--------------------------------------------------------------------------
| SHOP ROUTES - role:shop or admin
|--------------------------------------------------------------------------
*/

Route::prefix("shop")
  ->middleware(["auth", "role:shop,admin"])
  ->group(function () {
    Route::get("/", [ShopController::class, "dashboard"]);
    Route::get("/dashboard", [ShopController::class, "dashboard"])->name(
      "shop.dashboard"
    );
    Route::get("/requests", [ShopController::class, "requests"])->name(
      "shop.requests"
    );
    Route::get("/messages", [ShopController::class, "messages"])->name(
      "shop.messages"
    );
    Route::get("/jobs", [ShopController::class, "jobs"])->name("shop.jobs");
    Route::get("/reviews", [ShopController::class, "reviews"])->name(
      "shop.reviews"
    );
    Route::get("/settings", [ShopController::class, "settings"])->name(
      "shop.settings"
    );

    // Mechanic management
    Route::get("/mechanics", [ShopController::class, "mechanics"])->name(
      "shop.mechanics"
    );
    Route::post("/mechanics", [ShopController::class, "storeMechanic"])->name(
      "shop.mechanics.store"
    );
    Route::delete("/mechanics/{id}", [
      ShopController::class,
      "deleteMechanic",
    ])->name("shop.mechanics.delete");
    Route::post("/request/{id}/dispatch", [
      ShopController::class,
      "dispatchMechanic",
    ])->name("shop.dispatch-mechanic");

    Route::post("/accept/{id}", [ShopController::class, "accept"])->name(
      "shop.accept"
    );
    Route::post("/decline/{id}", [ShopController::class, "decline"])->name(
      "shop.decline"
    );
    Route::post("/request/{id}/status", [
      ShopController::class,
      "updateRequestStatus",
    ])->name("shop.update-status");
    Route::get("/dashboard-data", [
      ShopController::class,
      "fetchRequests",
    ])->name("shop.data");
    Route::get("/dashboard-map-data", [
      ShopController::class,
      "dashboardMapData",
    ])->name("shop.dashboard-map-data");
    Route::post("/settings/update", [ShopController::class, "update"])->name(
      "shop.update"
    );
    Route::post("/settings/images", [ShopController::class, "uploadImages"])->name(
      "shop.upload-images"
    );
    Route::post("/settings/toggle-status", [
      ShopController::class,
      "toggleStatus",
    ])->name("shop.toggle-status");
  });

/*
|--------------------------------------------------------------------------
| MECHANIC ROUTES - role:mechanic only
|--------------------------------------------------------------------------
*/

Route::prefix("mechanic")
  ->middleware(["auth", "role:mechanic"])
  ->group(function () {
    Route::get("/", [MechanicController::class, "dashboard"])->name(
      "mechanic.dashboard"
    );
    Route::get("/dashboard", [MechanicController::class, "dashboard"]);
    Route::get("/profile", [MechanicController::class, "profile"])->name(
      "mechanic.profile"
    );
    Route::post("/profile", [MechanicController::class, "updateProfile"])->name(
      "mechanic.profile.update"
    );
  });

/*
|--------------------------------------------------------------------------
| MOTORIST ROUTES - public (guest/motorist)
|--------------------------------------------------------------------------
*/

Route::prefix("motorist")->group(function () {
  // Map view
  Route::get("/map", [MotoristController::class, "map"])
    ->name("motorist.map");

  // Public / guest routes
  Route::get("/", [MotoristController::class, "index"])->name("motorist.index");
  Route::get("/shops", [MotoristController::class, "shops"])->name(
    "motorist.shops"
  );
  Route::get("/shop/{id}", [MotoristController::class, "showShop"])->name(
    "motorist.shop.show"
  );
  Route::post("/dispatch", [MotoristController::class, "storeDispatch"])->name(
    "motorist.dispatch.store"
  );
  Route::get("/request/{id}", [
    MotoristController::class,
    "requestStatus",
  ])->name("motorist.request.status");
  Route::post("/request/{id}/cancel", [MotoristController::class, "cancelDispatch"])->name("motorist.request.cancel");
  Route::post("/profile/password", [MotoristController::class, "changePassword"])->middleware(['auth', 'role:motorist'])->name("motorist.profile.password");
  Route::post("/review", [MotoristController::class, "storeReview"])->name(
    "motorist.review.store"
  );
});

// Motorist authenticated dashboard
Route::prefix("motorist")
  ->middleware(["auth", "role:motorist"])
  ->group(function () {
    Route::get("/dashboard", [MotoristController::class, "dashboard"])->name(
      "motorist.dashboard"
    );
  });

/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix("api")->group(function () {
  // Motorist API - public
  Route::get("/motorist/shops", [MotoristController::class, "apiShops"])->name(
    "api.motorist.shops"
  );
  Route::post("/dispatch-requests", [
    MotoristController::class,
    "createDispatchRequest",
  ])->name("api.dispatch.create");
  Route::get("/chat/{dispatchId}", [
    MotoristController::class,
    "getMessages",
  ])->name("api.motorist.chat");
  Route::post("/messages", [MotoristController::class, "sendMessage"])->name(
    "api.motorist.messages.send"
  );
  Route::post("/reviews", [MotoristController::class, "submitReview"])->name(
    "api.motorist.reviews.submit"
  );
  Route::get("/motorist/shops-for-messaging", [
    MotoristController::class,
    "getShopsForMessaging",
  ])->name("api.motorist.shops-for-messaging");
  Route::get("/motorist/shop-messages/{shopId}", [
    MotoristController::class,
    "getShopMessages",
  ])->name("api.motorist.shop-messages");
  Route::post("/motorist/shop-messages", [
    MotoristController::class,
    "sendShopMessage",
  ])->name("api.motorist.send-shop-message");

  // Shop API - role:shop or admin
  Route::middleware(["auth", "role:shop,admin"])->group(function () {
    Route::get("/shop/status", [ShopController::class, "getStatus"])->name(
      "api.shop.status"
    );
    Route::get("/shop/messages/{dispatchId}", [
      ShopController::class,
      "getMessages",
    ])->name("api.shop.messages");
    Route::post("/shop/messages/send", [
      ShopController::class,
      "sendMessage",
    ])->name("api.shop.messages.send");
  });
});
