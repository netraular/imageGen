@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Conceptos</h1>
    <a href="{{ route('concepts.create') }}" class="btn btn-primary">Agregar Nuevo Concepto</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($concepts as $concept)
            <tr>
                <td>{{ $concept->id }}</td>
                <td>{{ $concept->name }}</td>
                <td>{{ $concept->category->name }}</td>
                <td>
                    <form action="{{ route('concepts.destroy', $concept->id) }}" method="POST">
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