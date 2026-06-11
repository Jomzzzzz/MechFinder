@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="text-[#F4B942] text-2xl heading-font">Add Map Location</div>
        <div class="text-[#AAA] text-sm mt-1">Create a new map location and save it to the database.</div>
    </div>

    @if ($errors->any())
        <div class="bg-red-900 mb-4 p-4 rounded text-red-200 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.maps.store') }}" method="POST">
        @csrf
        @include('admin.maps.form')
    </form>
@endsection
