<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    protected function getCurrentShopId()
    {
        $user = Auth::user();

        if ($user && !empty($user->shop_id)) {
            return $user->shop_id;
        }

        $shop = DB::table('shops')
            ->where('owner_id', $user?->id)
            ->first();

        if ($shop) {
            return $shop->id;
        }

        return 1;
    }

    protected function getShop()
    {
        return DB::table('shops')
            ->where('id', $this->getCurrentShopId())
            ->first();
    }

    protected function getShopStatus()
    {
        $shop = $this->getShop();

        return $shop ? strtolower($shop->status) : 'closed';
    }

    public function dashboard()
    {
        $shopId = $this->getCurrentShopId();

        $requests = DB::table('dispatch_requests')
            ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
            ->where('dispatch_requests.shop_id', $shopId)
            ->where('dispatch_requests.status', 'requested')
            ->select(
                'dispatch_requests.*',
                DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
            )
            ->latest('dispatch_requests.created_at')
            ->get();

        $jobs = DB::table('dispatch_requests')
            ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
            ->where('dispatch_requests.shop_id', $shopId)
            ->whereIn('dispatch_requests.status', ['accepted', 'en_route', 'arrived', 'in_progress'])
            ->select(
                'dispatch_requests.*',
                DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
            )
            ->latest('dispatch_requests.updated_at')
            ->get();

        $pending = $requests->count();

        $jobsToday = DB::table('dispatch_requests')
            ->where('shop_id', $shopId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $averageRating = DB::table('reviews')
            ->where('shop_id', $shopId)
            ->avg('rating');

        $averageRating = $averageRating ? round($averageRating, 1) : 0;

        $activeJobsCount = DB::table('dispatch_requests')
            ->where('shop_id', $shopId)
            ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress'])
            ->count();

        $shop = $this->getShop();
$shopStatus = $this->getShopStatus();

$needsProfileCompletion = !$shop
    || empty($shop->shop_name)
    || empty($shop->address)
    || empty($shop->phone);

return view('shop.dashboard', compact(
    'requests',
    'jobs',
    'pending',
    'jobsToday',
    'averageRating',
    'activeJobsCount',
    'shop',
    'shopStatus',
    'needsProfileCompletion'
));
    }

    public function fetchRequests()
    {
        $shopId = $this->getCurrentShopId();

        $requests = DB::table('dispatch_requests')
            ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
            ->where('dispatch_requests.shop_id', $shopId)
            ->where('dispatch_requests.status', 'requested')
            ->select(
                'dispatch_requests.*',
                DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
            )
            ->latest('dispatch_requests.created_at')
            ->get();

        $html = view('components.requests-list', compact('requests'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'pending' => $requests->count(),
            'shopStatus' => $this->getShopStatus(),
        ]);
    }

    public function requests(Request $request)
{
    $shopId = $this->getCurrentShopId();

    $status = $request->query('status');

    $query = DB::table('dispatch_requests')
        ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
        ->where('dispatch_requests.shop_id', $shopId)
        ->select(
            'dispatch_requests.*',
            DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
        );

    // ✅ Apply filter if selected
    if (!empty($status)) {
        $query->where('dispatch_requests.status', $status);
    }

    $data = $query
        ->latest('dispatch_requests.created_at')
        ->get();

    return view('shop.requests', compact('data'));
}

    public function fetchActiveJobs()
    {
        $shopId = $this->getCurrentShopId();

        $jobs = DB::table('dispatch_requests')
            ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
            ->where('dispatch_requests.shop_id', $shopId)
            ->whereIn('dispatch_requests.status', ['accepted', 'en_route', 'arrived', 'in_progress'])
            ->select(
                'dispatch_requests.*',
                DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
            )
            ->latest('dispatch_requests.updated_at')
            ->get();

        return view('components.active-jobs-list', compact('jobs'));
    }

  public function dashboardMapData()
{
    $shopId = $this->getCurrentShopId();

    $shop = DB::table('shops')
        ->where('id', $shopId)
        ->select(
            'id',
            'shop_name',
            'address',
            'location',
            'latitude',
            'longitude',
            'status'
        )
        ->first();

    $requests = DB::table('dispatch_requests')
        ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
        ->where('dispatch_requests.shop_id', $shopId)
        ->whereIn('dispatch_requests.status', [
            'requested',
            'accepted',
            'en_route',
            'arrived',
            'in_progress'
        ])
        ->whereNotNull('dispatch_requests.latitude')
        ->whereNotNull('dispatch_requests.longitude')
        ->where('dispatch_requests.latitude', '!=', '')
        ->where('dispatch_requests.longitude', '!=', '')
        ->select(
            'dispatch_requests.id',
            'dispatch_requests.issue_type',
            'dispatch_requests.description',
            'dispatch_requests.status',
            'dispatch_requests.latitude',
            'dispatch_requests.longitude',
            'dispatch_requests.location',
            'dispatch_requests.guest_name',
            'dispatch_requests.motorist_uid',
            'dispatch_requests.motor_type',
            DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
        )
        ->latest('dispatch_requests.created_at')
        ->get();

    return response()->json([
        'success' => true,
        'shop' => $shop,
        'requests' => $requests
    ]);
}

    public function accept(int $id)
    {
        DB::table('dispatch_requests')
            ->where('id', $id)
            ->where('shop_id', $this->getCurrentShopId())
            ->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'updated_at' => now()
            ]);

        return back();
    }

    public function decline(int $id)
    {
        DB::table('dispatch_requests')
            ->where('id', $id)
            ->where('shop_id', $this->getCurrentShopId())
            ->update([
                'status' => 'declined',
                'updated_at' => now()
            ]);

        return back();
    }

    public function updateRequestStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:requested,accepted,en_route,arrived,in_progress,completed,declined'
        ]);

        $updateData = [
            'status' => $validated['status'],
            'updated_at' => now()
        ];

        if ($validated['status'] === 'accepted') {
            $updateData['accepted_at'] = now();
        }

        if ($validated['status'] === 'en_route') {
            $updateData['en_route_at'] = now();
        }

        if ($validated['status'] === 'arrived') {
            $updateData['arrived_at'] = now();
        }

        if ($validated['status'] === 'completed') {
            $updateData['completed_at'] = now();
        }

        DB::table('dispatch_requests')
            ->where('id', $id)
            ->where('shop_id', $this->getCurrentShopId())
            ->update($updateData);

        return response()->json([
            'success' => true,
            'status' => $validated['status']
        ]);
    }

    public function update(Request $request)
    {
        $shopId = $this->getCurrentShopId();

        $validated = $request->validate([
            'shop_name' => 'required|string|max:150',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:open,busy,closed,maintenance',
        ]);

        DB::table('shops')
            ->where('id', $shopId)
            ->update([
                'shop_name' => $validated['shop_name'],
                'address' => $validated['address'],
                'location' => $validated['address'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'status' => $validated['status'],
                'updated_at' => now()
            ]);

        return back()->with('success', 'Shop settings updated successfully.');
    }

    public function messages()
    {
        $shopId = $this->getCurrentShopId();

        $conversations = DB::table('dispatch_requests')
            ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
            ->where('dispatch_requests.shop_id', $shopId)
            ->select(
                'dispatch_requests.id as dispatch_id',
                'dispatch_requests.issue_type',
                'dispatch_requests.status',
                'dispatch_requests.guest_name',
                'dispatch_requests.motorist_uid',
                'dispatch_requests.motor_type',
                DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
            )
            ->latest('dispatch_requests.updated_at')
            ->get();

        $shopStatus = $this->getShopStatus();

        return view('shop.messages', compact('conversations', 'shopStatus'));
    }

   public function reviews()
{
    $shopId = $this->getCurrentShopId();

    $reviews = DB::table('reviews')
        ->leftJoin('users', 'reviews.motorist_id', '=', 'users.id')
        ->leftJoin('dispatch_requests', 'reviews.dispatch_id', '=', 'dispatch_requests.id')
        ->where('reviews.shop_id', $shopId)
        ->select(
            'reviews.id',
            'reviews.rating',
            'reviews.comment',
            'reviews.created_at',
            'dispatch_requests.issue_type',
            'dispatch_requests.request_type',
            DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Guest Motorist") as motorist_name')
        )
        ->orderBy('reviews.created_at', 'desc')
        ->get();

    // ✅ safe calculations (no crash if empty)
    $totalReviews = $reviews->count();
    $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

    $positivePercentage = $totalReviews > 0
        ? round(($reviews->where('rating', '>=', 4)->count() / $totalReviews) * 100)
        : 0;

    $ratingBreakdown = [
        5 => $reviews->where('rating', 5)->count(),
        4 => $reviews->where('rating', 4)->count(),
        3 => $reviews->where('rating', 3)->count(),
        2 => $reviews->where('rating', 2)->count(),
        1 => $reviews->where('rating', 1)->count(),
    ];

    $shopStatus = $this->getShopStatus();

    return view('shop.reviews', compact(
        'reviews',
        'totalReviews',
        'averageRating',
        'positivePercentage',
        'ratingBreakdown',
        'shopStatus'
    ));
}

    public function settings()
    {
        $shop = $this->getShop();
        $shopStatus = $this->getShopStatus();

        return view('shop.settings', compact('shop', 'shopStatus'));
    }

    public function toggleStatus()
    {
        $shopId = $this->getCurrentShopId();

        $shop = DB::table('shops')
            ->where('id', $shopId)
            ->first();

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found.'
            ], 404);
        }

        $newStatus = $shop->status === 'open' ? 'closed' : 'open';

        DB::table('shops')
            ->where('id', $shopId)
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus
        ]);
    }

    public function getStatus()
    {
        return response()->json([
            'success' => true,
            'status' => $this->getShopStatus()
        ]);
    }

    public function getMessages(int $dispatchId)
{
    $shopId = $this->getCurrentShopId();

    $messages = DB::table('messages')
        ->where('dispatch_id', $dispatchId)
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json([
        'success' => true,
        'messages' => $messages
    ]);
}

public function sendMessage(Request $request)
{
    $request->validate([
        'dispatch_id' => 'required',
        'message' => 'required|string|max:1000'
    ]);

    DB::table('messages')->insert([
        'dispatch_id' => $request->dispatch_id,
        'shop_id' => $this->getCurrentShopId(),
        'sender_type' => 'shop',
        'message' => $request->message,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true
    ]);
}

    public function jobs()
    {
        $shopId = $this->getCurrentShopId();

        $jobs = DB::table('dispatch_requests')
            ->leftJoin('users', 'dispatch_requests.motorist_id', '=', 'users.id')
            ->where('dispatch_requests.shop_id', $shopId)
            ->whereIn('dispatch_requests.status', ['accepted', 'en_route', 'arrived', 'in_progress', 'completed'])
            ->select(
                'dispatch_requests.*',
                DB::raw('COALESCE(users.name, dispatch_requests.guest_name, "Unknown Motorist") as motorist_name')
            )
            ->latest('dispatch_requests.created_at')
            ->get();

        $activeCount = DB::table('dispatch_requests')
            ->where('shop_id', $shopId)
            ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress'])
            ->count();

        $completedToday = DB::table('dispatch_requests')
            ->where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('completed_at', now()->toDateString())
            ->count();

        $totalJobs = DB::table('dispatch_requests')
            ->where('shop_id', $shopId)
            ->where('status', 'completed')
            ->count();

        $totalEarnings = DB::table('dispatch_requests')
            ->where('shop_id', $shopId)
            ->where('status', 'completed')
            ->sum('price') ?? 0;

        $shopStatus = $this->getShopStatus();

        return view('shop.jobs', compact(
            'jobs',
            'activeCount',
            'completedToday',
            'totalJobs',
            'totalEarnings',
            'shopStatus'
        ));
    }
}

