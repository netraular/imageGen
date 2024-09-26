@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Prompts</h1>
    <a href="{{ route('prompts.create') }}" class="btn btn-primary">Crear Nuevo Prompt</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Frase</th>
                <th>Plantilla</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prompts as $prompt)
            <tr>
                <td>{{ $prompt->id }}</td>
                <td>{{ $prompt->sentence }}</td>
                <td>{{ $prompt->template->sentence }}</td>
                <td>
                    <a href="{{ route('prompts.edit', $prompt->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('prompts.destroy', $prompt->id) }}" method="POST" style="display:inline;">
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