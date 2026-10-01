@extends('layouts.app')

@section('content')
    <h1>Daftar Kegiatan</h1>

    @if (session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('activities.create') }}">Tambah Kegiatan</a>
    </div>

    <!-- Search, Filter, Sort Form (Experiment 3) -->
    <form action="{{ route('activities.index') }}" method="GET" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ccc;">
        <div style="margin-bottom: 10px;">
            <label for="search">Cari (Code / Title):</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}">
        </div>
        
        <div style="margin-bottom: 10px;">
            <label for="category_id">Kategori:</label>
            <select name="category_id" id="category_id">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <strong>Urutkan:</strong>
            <label>
                <input type="radio" name="sort" value="newest" {{ request('sort', 'newest') == 'newest' ? 'checked' : '' }}> Terbaru
            </label>
            <label>
                <input type="radio" name="sort" value="oldest" {{ request('sort') == 'oldest' ? 'checked' : '' }}> Terlama
            </label>
        </div>

        <button type="submit">Terapkan Filter</button>
        <a href="{{ route('activities.index') }}">Reset</a>
    </form>

    <div style="margin-bottom: 20px;">
        <!-- Pagination Links -->
        {{ $activities->links() }}
    </div>

    @forelse ($activities as $activity)
        <article class="card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
            <h2>
                <a href="{{ route('activities.show', $activity) }}">
                    [{{ $activity->code }}] {{ $activity->title }}
                </a>
            </h2>
            <p><strong>Kategori:</strong> {{ $activity->category ? $activity->category->name : 'N/A' }}</p>
            <p><strong>Mulai:</strong> {{ $activity->start_at->format('d M Y') }}</p>
            <p><strong>Selesai:</strong> {{ $activity->end_at->format('d M Y') }}</p>
            <p><strong>Kapasitas:</strong> {{ $activity->capacity }}</p>
            <p><strong>Status:</strong> <span style="background-color: #eee; padding: 3px 8px; border-radius: 4px;">{{ $activity->status }}</span></p>
        </article>
    @empty
        <p>Belum ada kegiatan.</p>
    @endforelse

    <div style="margin-top: 20px;">
        <!-- Pagination Links -->
        {{ $activities->links() }}
    </div>
@endsection
