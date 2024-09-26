@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Editar Concepto</h1>
    <form action="{{ route('concepts.update', $concept->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $concept->name }}" required>
        </div>
        <div class="form-group">
            <label for="category_id">Categoría</label>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $concept->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="parent_id">Concepto Padre (Opcional)</label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">Ninguno</option>
                @foreach($concepts as $parentConcept)
                <option value="{{ $parentConcept->id }}" {{ $concept->parent_id == $parentConcept->id ? 'selected' : '' }}>{{ $parentConcept->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection