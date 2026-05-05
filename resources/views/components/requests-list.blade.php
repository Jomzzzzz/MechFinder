@forelse($requests as $req)

    @include('components.request-item', [
        'id' => $req->id,
        'issue' => $req->issue_type,
        'created_at' => $req->created_at,
        'motorist_name' => $req->motorist_name ?? 'Unknown',
        'motor_name' => $req->motor_name,
        'motor_brand' => $req->motor_brand,
        'motor_color' => $req->motor_color
    ])

@empty
    <p class="text-gray-500 text-sm">No requests</p>
@endforelse