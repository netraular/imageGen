@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Plantillas</h1>
    <a href="{{ route('templates.create') }}" class="btn btn-primary">Crear Nueva Plantilla</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Frase</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
            <tr>
                <td>{{ $template->id }}</td>
                <td>{{ $template->sentence }}</td>
                <td>
                    <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('templates.destroy', $template->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                    <form action="{{ route('templates.generate') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                        <button type="submit" class="btn btn-success">Generar Prompts</button>
                    </form>
                    <form action="{{ route('templates.executePrompts') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                        <button type="submit" class="btn btn-primary">Ejecutar Prompts</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection