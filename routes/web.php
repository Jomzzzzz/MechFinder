<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MotoristController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;

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

Route::get("/signup", [AuthController::class, "showSignup"])->name("signup");
Route::post("/signup", [AuthController::class, "register"])->name(
  "signup.post"
);

Route::get("/auth/google/login", [
  AuthController::class,
  "redirectToGoogleLogin",
])->name("auth.google.login");
Route::get("/auth/google/signup", [
  AuthController::class,
  "redirectToGoogleSignup",
])->name("auth.google.signup");
Route::get("/auth/google/callback", [
  AuthController::class,
  "handleGoogleCallback",
])->name("auth.google.callback");

Route::post("/logout", [AuthController::class, "logout"])->name("logout");

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
    Route::post("/settings/toggle-status", [
      ShopController::class,
      "toggleStatus",
    ])->name("shop.toggle-status");
  });

/*
|--------------------------------------------------------------------------
| MOTORIST ROUTES - public (guest/user)
|--------------------------------------------------------------------------
*/

Route::prefix("motorist")->group(function () {
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
  Route::post("/review", [MotoristController::class, "storeReview"])->name(
    "motorist.review.store"
  );
});

/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix("api")->group(function () {
  /*
    |--------------------------------------------------------------------------
    | MOTORIST API - public
    |--------------------------------------------------------------------------
    */

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

  /*
    |--------------------------------------------------------------------------
    | SHOP API - role:shop or admin
    |--------------------------------------------------------------------------
    */

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
