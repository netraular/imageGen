@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Detalles de la Combinación #{{ $combination->id }}</h1>
    <p><strong>Descripción:</strong> {{ $combination->description }}</p>
    <p><strong>Generada:</strong> {{ $combination->is_generated ? 'Sí' : 'No' }}</p>
    <a href="{{ route('combinations.index') }}" class="btn btn-secondary">Volver a Combinaciones</a>
    <a href="{{ route('combinations.edit', $combination->id) }}" class="btn btn-warning">Editar</a>
    <form action="{{ route('combinations.destroy', $combination->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Eliminar</button>
    </form>
</div>
@endsection