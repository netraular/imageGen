@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Crear Nueva Plantilla</h1>
    <form action="{{ route('templates.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="sentence">Frase</label>
            <input type="text" name="sentence" id="sentence" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection