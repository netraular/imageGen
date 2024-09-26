@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Valores</h1>
    <a href="{{ route('values.create') }}" class="btn btn-primary">Agregar Nuevo Valor</a>
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
            @foreach($values as $value)
            <tr>
                <td>{{ $value->id }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->category->name }}</td>
                <td>
                    <a href="{{ route('values.edit', $value->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('values.destroy', $value->id) }}" method="POST" style="display:inline;">
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