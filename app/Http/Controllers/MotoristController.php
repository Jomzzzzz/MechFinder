<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotoristController extends Controller
{
    public function index()
    {
        return view('motorist.index');
    }

    public function shops(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $shops = DB::table('shops')
            ->leftJoin('reviews', 'shops.id', '=', 'reviews.shop_id')
            ->select(
                'shops.id',
                'shops.name',
                'shops.address',
                'shops.phone',
                'shops.latitude',
                'shops.longitude',
                'shops.status',
                DB::raw('COALESCE(AVG(reviews.rating), 0) as rating'),
                DB::raw('COUNT(reviews.id) as review_count')
            )
            ->groupBy(
                'shops.id',
                'shops.name',
                'shops.address',
                'shops.phone',
                'shops.latitude',
                'shops.longitude',
                'shops.status'
            )
            ->get()
            ->map(function ($shop) use ($lat, $lng) {
                $shop->rating = round($shop->rating, 1);

                if ($lat && $lng && $shop->latitude && $shop->longitude) {
                    $shop->distance = $this->distanceKm($lat, $lng, $shop->latitude, $shop->longitude);
                    $shop->eta = max(3, round(($shop->distance / 30) * 60));
                } else {
                    $shop->distance = null;
                    $shop->eta = null;
                }

                return $shop;
            })
            ->sortBy(function ($shop) {
                if ($shop->status === 'open') {
                    return $shop->distance ?? 999;
                }

                return 9999;
            })
            ->values();

        return response()->json($shops);
    }

    public function showShop($id)
    {
        $shop = DB::table('shops')->where('id', $id)->first();

        if (!$shop) {
            abort(404);
        }

        $reviews = DB::table('reviews')
            ->where('shop_id', $id)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'shop' => $shop,
            'reviews' => $reviews,
        ]);
    }

    public function storeDispatch(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'guest_token' => 'nullable|string|max:100',
            'owner_name' => 'required|string|max:150',
            'contact_number' => 'required|string|max:50',
            'vehicle_make_model' => 'nullable|string|max:150',
            'vehicle_variant_color' => 'nullable|string|max:150',
            'plate_temp_number' => 'nullable|string|max:80',
            'issue_type' => 'required|string|max:150',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $id = DB::table('dispatch_requests')->insertGetId([
            'shop_id' => $validated['shop_id'],
            'guest_token' => $validated['guest_token'] ?? null,
            'owner_name' => $validated['owner_name'],
            'contact_number' => $validated['contact_number'],
            'vehicle_make_model' => $validated['vehicle_make_model'] ?? null,
            'vehicle_variant_color' => $validated['vehicle_variant_color'] ?? null,
            'plate_temp_number' => $validated['plate_temp_number'] ?? null,
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => 'requested',
            'price' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'request_id' => $id,
            'message' => 'Dispatch request sent successfully.',
        ]);
    }

    public function requestStatus($id)
    {
        $request = DB::table('dispatch_requests')
            ->join('shops', 'dispatch_requests.shop_id', '=', 'shops.id')
            ->where('dispatch_requests.id', $id)
            ->select(
                'dispatch_requests.*',
                'shops.name as shop_name',
                'shops.phone as shop_phone',
                'shops.address as shop_address'
            )
            ->first();

        if (!$request) {
            return response()->json(['error' => 'Request not found.'], 404);
        }

        return response()->json($request);
    }

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'guest_token' => 'nullable|string|max:100',
            'owner_name' => 'nullable|string|max:150',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        DB::table('reviews')->insert([
            'shop_id' => $validated['shop_id'],
            'guest_token' => $validated['guest_token'] ?? null,
            'owner_name' => $validated['owner_name'] ?? 'Motorist',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.',
        ]);
    }

    private function distanceKm($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) *
            sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}