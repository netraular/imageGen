@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Combinaciones</h1>
    <a href="{{ route('combinations.create') }}" class="btn btn-primary">Crear Nueva Combinación</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Frase</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($combinations as $combination)
            <tr>
                <td>{{ $combination->id }}</td>
                <td>{{ $combination->sentence }}</td>
                <td>
                    <a href="{{ route('combinations.edit', $combination->id) }}" class="btn btn-warning">Editar</a>
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