@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-4">Bulk Invite Users to {{ $school->name }}</h1>
        @if(session('success'))
            <div class="p-4 mb-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('bulk-invite.upload', $school->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block font-medium mb-1">CSV File</label>
                <input type="file" name="csv_file" required class="border rounded p-2 w-full">
                <p class="text-sm text-gray-500 mt-1">CSV must have columns: <code>email</code>, <code>role</code></p>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Upload & Invite</button>
        </form>
    </div>
@endsection 