@extends('layouts.app')

@section('content')
    <h1>Kategori</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if (session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('categories.store') }}" method="POST" style="margin-bottom: 20px;">
        @csrf
        <label for="name">Nama</label>
        <input id="name" name="name" value="{{ old('name') }}" required>
        <label for="slug">Slug</label>
        <input id="slug" name="slug" value="{{ old('slug') }}" required>
        <button type="submit">Tambah kategori</button>
    </form>

    @foreach ($categories as $category)
        <article style="margin-bottom: 12px;">
            <strong>{{ $category->name }}</strong>
            <span>({{ $category->activities_count }} kegiatan)</span>
            <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Hapus</button>
            </form>
        </article>
    @endforeach

    <a href="{{ route('activities.index') }}">Kembali ke kegiatan</a>
@endsection
