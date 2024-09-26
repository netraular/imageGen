@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Generaciones</h1>
    <a href="{{ route('generations.create') }}" class="btn btn-primary">Crear Nueva Generación</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Frase</th>
                <th>Combinación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($generations as $generation)
            <tr>
                <td>{{ $generation->id }}</td>
                <td>{{ $generation->sentence }}</td>
                <td>{{ $generation->combination->sentence }}</td>
                <td>
                    <a href="{{ route('generations.edit', $generation->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('generations.destroy', $generation->id) }}" method="POST" style="display:inline;">
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