@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Combinaciones</h1>
    <a href="{{ route('combinations.create') }}" class="btn btn-primary">Generar Nueva Combinación</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Generada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($combinations as $combination)
            <tr>
                <td>{{ $combination->id }}</td>
                <td>{{ $combination->description }}</td>
                <td>{{ $combination->is_generated ? 'Sí' : 'No' }}</td>
                <td>
                    <a href="{{ route('combinations.show', $combination->id) }}" class="btn btn-info">Ver Respuestas</a>
                    <form action="{{ route('combinations.destroy', $combination->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection