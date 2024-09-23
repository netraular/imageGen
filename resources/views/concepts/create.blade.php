@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agregar Nuevo Concepto</h1>
    <form action="{{ route('concepts.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="category_id">Categoría</label>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="parent_id">Concepto Padre (Opcional)</label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">Ninguno</option>
                @foreach($concepts as $concept)
                <option value="{{ $concept->id }}">{{ $concept->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection