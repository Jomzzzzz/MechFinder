<?php

namespace App\Http\Controllers;

use App\Events\DispatchMessageSent;
use App\Events\DispatchStatusUpdated;
use App\Events\MechanicLocationUpdated;
use App\Events\ShopStatusUpdated;
use App\Models\DispatchMechanic;
use App\Models\DispatchRequest;
use App\Models\MechanicProfile;
use App\Models\Message;
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

    $shopConversations = $jobs
      ->filter(fn ($job) => $job->dispatchRequest && $job->dispatchRequest->shop)
      ->map(function ($job) {
        $dispatch = $job->dispatchRequest;

        return [
          "dispatch_id" => $dispatch->id,
          "shop_name" => optional($dispatch->shop)->shop_name ?? "Shop",
          "motorist_name" => optional($dispatch->motorist)->name ?? $dispatch->guest_name ?? "Motorist",
          "issue_type" => $dispatch->issue_type ?? "New request",
          "status" => $dispatch->status,
          "unread_count" => Message::where('dispatch_id', $dispatch->id)
              ->where('conversation_type', 'shop')
              ->where('sender_type', 'shop')
              ->where('is_read', false)
              ->count('id'),
        ];
      })
      ->values();

    $motoristConversations = $jobs
      ->filter(fn ($job) => $job->dispatchRequest)
      ->map(function ($job) {
        $dispatch = $job->dispatchRequest;

        return [
          "dispatch_id" => $dispatch->id,
          "motorist_name" => optional($dispatch->motorist)->name ?? $dispatch->guest_name ?? "Motorist",
          "shop_name" => optional($dispatch->shop)->shop_name ?? "Shop",
          "issue_type" => $dispatch->issue_type ?? "New request",
          "status" => $dispatch->status,
          "unread_count" => Message::where('dispatch_id', $dispatch->id)
              ->where('conversation_type', 'mechanic')
              ->where('sender_type', 'motorist')
              ->where('is_read', false)
              ->count('id'),
        ];
      })
      ->values();

    return view(
      "mechanic.dashboard-mobile",
      compact("jobs", "profile", "mechanic", "shopConversations", "motoristConversations")
    );
  }

  public function profile()
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

    $shopConversations = $jobs
      ->filter(fn ($job) => $job->dispatchRequest && $job->dispatchRequest->shop)
      ->map(function ($job) {
        $dispatch = $job->dispatchRequest;

        return [
          "dispatch_id" => $dispatch->id,
          "shop_name" => optional($dispatch->shop)->shop_name ?? "Shop",
          "motorist_name" => optional($dispatch->motorist)->name ?? $dispatch->guest_name ?? "Motorist",
          "issue_type" => $dispatch->issue_type ?? "New request",
          "status" => $dispatch->status,
          "unread_count" => Message::where('dispatch_id', $dispatch->id)
              ->where('conversation_type', 'shop')
              ->where('sender_type', 'shop')
              ->where('is_read', false)
              ->count('id'),
        ];
      })
      ->values();

    $motoristConversations = $jobs
      ->filter(fn ($job) => $job->dispatchRequest)
      ->map(function ($job) {
        $dispatch = $job->dispatchRequest;

        return [
          "dispatch_id" => $dispatch->id,
          "motorist_name" => optional($dispatch->motorist)->name ?? $dispatch->guest_name ?? "Motorist",
          "shop_name" => optional($dispatch->shop)->shop_name ?? "Shop",
          "issue_type" => $dispatch->issue_type ?? "New request",
          "status" => $dispatch->status,
          "unread_count" => Message::where('dispatch_id', $dispatch->id)
              ->where('conversation_type', 'mechanic')
              ->where('sender_type', 'motorist')
              ->where('is_read', false)
              ->count('id'),
        ];
      })
      ->values();

    return view(
      "mechanic.dashboard-mobile",
      compact("jobs", "profile", "mechanic", "shopConversations", "motoristConversations")
    );
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
      ->back()
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
      ->back()
      ->with("pw_success", "Password updated successfully.");
  }

  public function chat(int $dispatchId)
  {
    $mechanic = Auth::user();
    $job = DispatchMechanic::with(["dispatchRequest.shop"])
      ->where("mechanic_id", $mechanic->id)
      ->where("dispatch_request_id", $dispatchId)
      ->firstOrFail();

    $dispatch = $job->dispatchRequest;
    if (!$dispatch) {
      abort(404);
    }

    return view("mechanic.chat-mobile", compact("dispatch"));
  }

  public function messages()
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

    $shopConversations = $jobs
      ->filter(fn ($job) => $job->dispatchRequest && $job->dispatchRequest->shop)
      ->map(function ($job) {
        $dispatch = $job->dispatchRequest;

        return [
          "dispatch_id" => $dispatch->id,
          "shop_name" => optional($dispatch->shop)->shop_name ?? "Shop",
          "motorist_name" => optional($dispatch->motorist)->name ?? $dispatch->guest_name ?? "Motorist",
          "issue_type" => $dispatch->issue_type ?? "New request",
          "status" => $dispatch->status,
          "unread_count" => Message::where('dispatch_id', $dispatch->id)
              ->where('conversation_type', 'shop')
              ->where('sender_type', 'shop')
              ->where('is_read', false)
              ->count('id'),
        ];
      })
      ->values();

    $motoristConversations = $jobs
      ->filter(fn ($job) => $job->dispatchRequest)
      ->map(function ($job) {
        $dispatch = $job->dispatchRequest;

        return [
          "dispatch_id" => $dispatch->id,
          "motorist_name" => optional($dispatch->motorist)->name ?? $dispatch->guest_name ?? "Motorist",
          "shop_name" => optional($dispatch->shop)->shop_name ?? "Shop",
          "issue_type" => $dispatch->issue_type ?? "New request",
          "status" => $dispatch->status,
          "unread_count" => Message::where('dispatch_id', $dispatch->id)
              ->where('conversation_type', 'mechanic')
              ->where('sender_type', 'motorist')
              ->where('is_read', false)
              ->count('id'),
        ];
      })
      ->values();

    return view(
      "mechanic.dashboard-mobile",
      compact("jobs", "profile", "mechanic", "shopConversations", "motoristConversations")
    );
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

  public function updateLocation(Request $request, int $id)
  {
    $validated = $request->validate([
      "lat" => ["required", "numeric", "between:-90,90"],
      "lng" => ["required", "numeric", "between:-180,180"],
    ]);

    $mechanic = Auth::user();
    $job = DispatchMechanic::where("dispatch_request_id", $id)
      ->where("mechanic_id", $mechanic->id)
      ->firstOrFail();

    $dispatch = $job->dispatchRequest;
    if (!$dispatch || !in_array($dispatch->status, ["en_route", "arrived"])) {
      return response()->json(["success" => false, "message" => "Job not active."], 422);
    }

    DispatchRequest::where("id", $dispatch->id)->update([
      "mechanic_lat" => $validated["lat"],
      "mechanic_lng" => $validated["lng"],
      "mechanic_location_at" => now(),
    ]);

    broadcast(new MechanicLocationUpdated($dispatch->id, (float) $validated["lat"], (float) $validated["lng"]));

    return response()->json(["success" => true]);
  }

  public function getMessages(int $dispatchId)
  {
    $mechanic = Auth::user();

    // Verify mechanic has access to this dispatch
    $job = DispatchMechanic::where("dispatch_request_id", $dispatchId)
      ->where("mechanic_id", $mechanic->id)
      ->firstOrFail();

    $dispatch = DispatchRequest::findOrFail($dispatchId);

    $conversationType = request()->query('conversation_type');
    if ($conversationType === 'mechanic') {
      $conversationType = 'shop';
    }

    $shouldMarkRead = request()->query('mark_read') === '1';
    $unreadCount = 0;
    if (in_array($conversationType, ['shop', 'motorist'], true)) {
      // Normalize 'motorist' to 'mechanic' for motorist→mechanic messages
      $queryType = $conversationType === 'motorist' ? 'mechanic' : $conversationType;
      $markSender = $conversationType === 'shop' ? 'shop' : 'motorist';

      $unreadCount = Message::where('dispatch_id', $dispatchId)
        ->where('conversation_type', $queryType)
        ->where('sender_type', $markSender)
        ->where('is_read', false)
        ->count('id');

      if ($shouldMarkRead) {
        Message::where('dispatch_id', $dispatchId)
          ->where('conversation_type', $queryType)
          ->where('sender_type', $markSender)
          ->where('is_read', false)
          ->update(['is_read' => true]);
      }
    }

    // Fetch messages for this dispatch and selected conversation type
    $query = Message::where("dispatch_id", $dispatchId)
      ->with(['motorist', 'shop', 'mechanic'])
      ->latest('id');

    if (in_array($conversationType, ['shop', 'motorist', 'mechanic'], true)) {
      // Convert 'motorist' to 'mechanic' for querying motorist→mechanic messages
      $queryType = $conversationType === 'motorist' ? 'mechanic' : $conversationType;
      $query->where('conversation_type', $queryType);

      if ($queryType === 'mechanic') {
        // For motorist↔mechanic: show messages from both motorist and mechanic
        $query->whereIn('sender_type', ['motorist', 'mechanic']);
      } elseif ($queryType === 'shop') {
        // For shop↔mechanic: show messages from both shop and mechanic
        $query->whereIn('sender_type', ['shop', 'mechanic']);
      }
    }

    $messages = $query
      ->get()
      ->reverse()
      ->map(function ($msg) {
        return [
          'id' => $msg->id,
          'dispatch_id' => $msg->dispatch_id,
          'sender_type' => $msg->sender_type,
          'sender_name' => $msg->sender_name ?? match ($msg->sender_type) {
            'motorist' => $msg->motorist?->name ?? 'Motorist',
            'shop' => $msg->shop?->name ?? 'Shop',
            'mechanic' => $msg->mechanic?->name ?? 'Mechanic',
            default => 'Unknown'
          },
          'conversation_type' => $msg->conversation_type,
          'message' => $msg->message,
          'created_at' => $msg->created_at->toIso8601String(),
          'is_read' => $msg->is_read,
        ];
      })
      ->values();

    return response()->json([
      'success' => true,
        'unread_count' => $unreadCount,
      'messages' => $messages,
      'dispatch' => [
        'id' => $dispatch->id,
        'shop_name' => $dispatch->shop?->shop_name ?? 'Shop',
        'motorist_name' => $dispatch->motorist?->name ?? $dispatch->guest_name ?? 'Motorist',
      ],
    ]);
  }

  public function sendMessage(Request $request)
  {
    $mechanic = Auth::user();

    $validated = $request->validate([
      'dispatch_id' => ['required', 'integer', 'exists:dispatch_requests,id'],
      'message' => ['required', 'string', 'min:1', 'max:1000'],
      'conversation_type' => ['required', 'string', 'in:shop,motorist,mechanic'],
    ]);

    // Verify mechanic has access to this dispatch
    $job = DispatchMechanic::where("dispatch_request_id", $validated['dispatch_id'])
      ->where("mechanic_id", $mechanic->id)
      ->firstOrFail();

    $dispatch = DispatchRequest::findOrFail($validated['dispatch_id']);

    $conversationType = $validated['conversation_type'] === 'mechanic'
      ? 'shop'
      : ($validated['conversation_type'] === 'motorist' ? 'mechanic' : $validated['conversation_type']);

    // Create and save the message
    $message = Message::create([
      'dispatch_id' => $validated['dispatch_id'],
      'mechanic_id' => $mechanic->id,
      'shop_id' => $dispatch->shop_id,
      'motorist_id' => $dispatch->motorist_id,
      'message' => $validated['message'],
      'sender_type' => 'mechanic',
      'sender_name' => $mechanic->name,
      'conversation_type' => $conversationType,
      'is_read' => false,
    ]);

    // Broadcast the message event for real-time updates
    broadcast(new DispatchMessageSent($message->dispatch_id, [
      'id'                => $message->id,
      'dispatch_id'       => $message->dispatch_id,
      'sender_type'       => $message->sender_type,
      'sender_name'       => $message->sender_name,
      'conversation_type' => $message->conversation_type,
      'message'           => $message->message,
      'created_at'        => $message->created_at->toIso8601String(),
      'is_read'           => $message->is_read,
    ]))->toOthers();

    return response()->json([
      'success' => true,
      'message' => [
        'id' => $message->id,
        'dispatch_id' => $message->dispatch_id,
        'sender_type' => $message->sender_type,
        'sender_name' => $message->sender_name,
        'message' => $message->message,
        'created_at' => $message->created_at->toIso8601String(),
        'is_read' => $message->is_read,
      ],
    ]);
  }
}
