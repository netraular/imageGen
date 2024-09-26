@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Editar Combinación</h1>
    <form action="{{ route('combinations.update', $combination->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="description">Descripción</label>
            <input type="text" name="description" id="description" class="form-control" value="{{ $combination->description }}" required>
        </div>
        <div class="form-group">
            <label for="is_generated">Generada</label>
            <select name="is_generated" id="is_generated" class="form-control">
                <option value="0" {{ $combination->is_generated ? '' : 'selected' }}>No</option>
                <option value="1" {{ $combination->is_generated ? 'selected' : '' }}>Sí</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection