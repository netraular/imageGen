@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles de la Categoría</h1>
    <p><strong>ID:</strong> {{ $category->id }}</p>
    <p><strong>Nombre:</strong> {{ $category->name }}</p>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Volver a Categorías</a>
</div>
@endsection