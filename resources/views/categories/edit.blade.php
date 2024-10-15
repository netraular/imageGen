@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Categoría</h1>
    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ $category->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="order">Orden</label>
            <input type="number" name="order" id="order" class="form-control" value="{{ $category->order }}">
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection