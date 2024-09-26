@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas LLM</h1>
    <a href="{{ route('llm_responses.create') }}" class="btn btn-primary">Crear Nueva Respuesta LLM</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Respuesta</th>
                <th>Fuente</th>
                <th>Generación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($llmResponses as $llmResponse)
            <tr>
                <td>{{ $llmResponse->id }}</td>
                <td>{{ $llmResponse->response }}</td>
                <td>{{ $llmResponse->source }}</td>
                <td>{{ $llmResponse->generation->sentence }}</td>
                <td>
                    <a href="{{ route('llm_responses.edit', $llmResponse->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('llm_responses.destroy', $llmResponse->id) }}" method="POST" style="display:inline;">
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