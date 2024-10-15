@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas LLM</h1>
    <a href="{{ route('llm_responses.create') }}" class="btn btn-primary">Crear Nueva Respuesta LLM</a>
    <br><br>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Respuesta</th>
                <th>Fuente</th>
                <th>Prompt</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($llmResponses as $llmResponse)
            <tr>
                <td>{{ $llmResponse->id }}</td>
                <td>{{ $llmResponse->response }}</td>
                <td>{{ $llmResponse->source }}</td>
                <td>{{ $llmResponse->prompt->sentence }}</td>
                <td>
                    @if($llmResponse->status == 'pending')
                        <i class="fas fa-clock text-warning" data-toggle="tooltip" data-placement="top" title="Pendiente"></i>
                    @elseif($llmResponse->status == 'executing')
                        <i class="fas fa-spinner fa-spin text-info" data-toggle="tooltip" data-placement="top" title="Ejecutando"></i>
                    @elseif($llmResponse->status == 'success')
                        <i class="fas fa-check-circle text-success" data-toggle="tooltip" data-placement="top" title="Éxito"></i>
                    @elseif($llmResponse->status == 'error')
                        <i class="fas fa-exclamation-circle text-danger" data-toggle="tooltip" data-placement="top" title="Error"></i>
                    @endif
                </td>
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

@push('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endpush

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endpush