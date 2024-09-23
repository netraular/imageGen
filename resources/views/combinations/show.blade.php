@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles de la Combinación #{{ $combination->id }}</h1>
    <p><strong>Descripción:</strong> {{ $combination->description }}</p>
    <p><strong>Generada:</strong> {{ $combination->is_generated ? 'Sí' : 'No' }}</p>
    <a href="{{ route('combinations.index') }}" class="btn btn-secondary">Volver a Combinaciones</a>
    <a href="{{ route('responses.index', ['combination' => $combination->id]) }}" class="btn btn-info">Ver Respuestas</a>
</div>
@endsection