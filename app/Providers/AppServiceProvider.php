<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  public function register(): void {}

  public function boot(): void
  {
    // Share shop status config with every view that uses the shop layout.
    // Cached for 24 h — run `php artisan cache:clear` after editing the table.
    View::composer("layouts.shop", function ($view) {
      try {
        if (!Schema::hasTable("shop_statuses")) {
          $view->with("shopStatusConfigs", []);
          return;
        }

        $configs = Cache::remember(
          "shop_statuses_config",
          now()->addDay(),
          function () {
            return DB::table("shop_statuses")
              ->orderBy("sort_order")
              ->get([
                "id",
                "slug",
                "label",
                "color",
                "bg",
                "next_label",
                "next_color",
              ])
              ->keyBy("id")
              ->toArray();
          }
        );

        $view->with("shopStatusConfigs", $configs);
      } catch (\Throwable $e) {
        $view->with("shopStatusConfigs", []);
      }
    });
  }
}
