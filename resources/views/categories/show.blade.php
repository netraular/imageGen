@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles de la Categoría</h1>
    <p><strong>Nombre:</strong> {{ $category->name }}</p>
    <p><strong>Descripción:</strong> {{ $category->description }}</p>
    <p><strong>Orden:</strong> {{ $category->order }}</p>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Volver a Categorías</a>
</div>
@endsection