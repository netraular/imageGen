@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Elementos</h1>
    <a href="{{ route('elements.create') }}" class="btn btn-primary">Agregar Nuevo Elemento</a>
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
            @foreach($elements as $element)
            <tr>
                <td>{{ $element->id }}</td>
                <td>{{ $element->name }}</td>
                <td>{{ $element->category->name }}</td>
                <td>
                    <a href="{{ route('elements.edit', $element->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('elements.destroy', $element->id) }}" method="POST" style="display:inline;">
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