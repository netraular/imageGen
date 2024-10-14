@extends('layouts.app')

@section('content_body')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Profile</div>

                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control">{{ $user->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="profile_photo">Profile Photo URL</label>
                            <input type="text" name="profile_photo" id="profile_photo" class="form-control" value="{{ $user->profile_photo }}">
                        </div>

                        <div class="form-group">
                            <label for="llm_api_key">LLM API Key</label>
                            <input type="text" name="llm_api_key" id="llm_api_key" class="form-control" placeholder="Enter new API key to update">
                        </div>

                        <div class="form-group">
                            <label for="llm_service_name">LLM Service Name</label>
                            <input type="text" name="llm_service_name" id="llm_service_name" class="form-control" value="{{ $user->llm_service_name }}">
                        </div>

                        <div class="form-group">
                            <label for="comfyui_url">ComfyUI URL</label>
                            <input type="url" name="comfyui_url" id="comfyui_url" class="form-control" placeholder="Enter new ComfyUI URL to update">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection