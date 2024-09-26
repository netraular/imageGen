@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Editar Plantilla</h1>
    <form action="{{ route('templates.update', $template->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="sentence">Frase</label>
            <input type="text" name="sentence" id="sentence" class="form-control" value="{{ old('sentence', $template->sentence) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection