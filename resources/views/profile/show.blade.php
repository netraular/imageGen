@extends('layouts.app')

@section('content_body')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Profile</div>

                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $user->adminlte_image() }}" alt="Profile Photo" class="rounded-circle mr-3" width="50" height="50">
                        <div>
                            <h4>{{ $user->name }}</h4>
                            <p>{{ $user->email }}</p>
                        </div>
                    </div>

                    <p><strong>Description:</strong> {{ $user->description ?? 'No description' }}</p>
                    <p><strong>LLM API Key:</strong> {{ $user->masked_llm_api_key }}</p>
                    <p><strong>LLM Service Name:</strong> {{ $user->llm_service_name ?? 'Not set' }}</p>
                    <p><strong>ComfyUI URL:</strong> <span class="text-muted">Hidden</span></p>
                    <p><strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i:s') }}</p>

                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection