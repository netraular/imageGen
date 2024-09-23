@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas para la Combinación #{{ $combination->id }}</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Respuesta</th>
                <th>Fuente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($responses as $response)
            <tr>
                <td>{{ $response->id }}</td>
                <td>{{ $response->response }}</td>
                <td>{{ $response->source }}</td>
                <td>
                    <form action="{{ route('responses.destroy', $response->id) }}" method="POST" style="display:inline;">
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