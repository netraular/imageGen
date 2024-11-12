@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Test Page</h1>
    <p>This is a test page.</p>
    <a href="{{ route('test.api') }}" class="btn btn-primary">Run API Test</a>
</div>
@endsection