@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Edit Kategori</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </form>
        </div>
    </div>
</div>
@endsection
