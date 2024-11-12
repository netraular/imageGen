@extends('layouts.app')

@section('content')
<div class="container">
    <h1>API Test Result</h1>
    @if(isset($responseData['error']))
        <p class="text-danger">{{ $responseData['error'] }}</p>
    @else
        <pre>{{ json_encode($responseData, JSON_PRETTY_PRINT) }}</pre>
    @endif
    <a href="{{ route('test.index') }}" class="btn btn-secondary">Back to Test Page</a>
</div>
@endsection