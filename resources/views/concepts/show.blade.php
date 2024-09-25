@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Detalles del Concepto</h1>
    <p><strong>ID:</strong> {{ $concept->id }}</p>
    <p><strong>Nombre:</strong> {{ $concept->name }}</p>
    <p><strong>Categoría:</strong> {{ $concept->category->name }}</p>
    <p><strong>Concepto Padre:</strong> {{ $concept->parent ? $concept->parent->name : 'Ninguno' }}</p>
    <a href="{{ route('concepts.index') }}" class="btn btn-secondary">Volver a Conceptos</a>
</div>
@endsection