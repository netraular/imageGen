@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Crear Nueva Combinación</h1>
    <form action="{{ route('combinations.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="description">Descripción</label>
            <input type="text" name="description" id="description" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="is_generated">Generada</label>
            <select name="is_generated" id="is_generated" class="form-control">
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection