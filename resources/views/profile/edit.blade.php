@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white d-flex align-items-center">
                    <img src="{{ $user->profile_image_url }}" alt="Profile Image" class="rounded-circle me-3" width="80" height="80" style="object-fit:cover;">
                    <div>
                        <h4 class="mb-0">Edit Profile</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" name="profile_image" id="profile_image" class="form-control">
                            @if($user->profile_image)
                                <img src="{{ $user->profile_image_url }}" alt="Current Profile Image" class="rounded mt-2" width="80" height="80" style="object-fit:cover;">
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 