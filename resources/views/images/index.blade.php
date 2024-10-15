@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Imágenes</h1>
    <a href="{{ route('images.create') }}" class="btn btn-primary">Crear Nueva Imagen</a>
    <br><br>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ruta de la Imagen</th>
                <th>Respuesta LLM</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($images as $image)
            <tr>
                <td>{{ $image->id }}</td>
                <td>{{ $image->image_path }}</td>
                <td>{{ $image->llmResponse->response }}</td>
                <td>
                    <a href="{{ route('images.edit', $image->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('images.destroy', $image->id) }}" method="POST" style="display:inline;">
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