@extends('layouts.app')

@section('content')
    <a href="{{ route('activities.index') }}">Kembali ke Daftar</a>
    
    @if (session('success'))
        <div style="color: green; margin-top: 15px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-top: 15px; margin-bottom: 15px;">
        <a href="{{ route('activities.edit', $activity) }}" style="margin-right: 10px;">Edit</a>

        @if ($activity->status === 'draft')
            <form action="{{ route('activities.publish', $activity) }}" method="POST" style="display: inline; margin-right: 10px;">
                @csrf
                <button type="submit">Publish</button>
            </form>
        @endif

        @if ($activity->status === 'published')
            <form action="{{ route('activities.complete', $activity) }}" method="POST" style="display: inline; margin-right: 10px;">
                @csrf
                <button type="submit">Selesaikan</button>
            </form>
        @endif
        
        <form action="{{ route('activities.destroy', $activity) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" style="color: red;">Hapus</button>
        </form>
    </div>

    <h1>[{{ $activity->code }}] {{ $activity->title }}</h1>
    
    <div>
        <p><strong>Kategori:</strong> {{ $activity->category ? $activity->category->name : 'N/A' }}</p>
        <p><strong>Tanggal Mulai:</strong> {{ $activity->start_at->format('d M Y') }}</p>
        <p><strong>Tanggal Selesai:</strong> {{ $activity->end_at->format('d M Y') }}</p>
        <p><strong>Kapasitas:</strong> {{ $activity->capacity }}</p>
        <p><strong>Pendaftar:</strong> {{ $activity->registered_count }} / {{ $activity->capacity }}</p>
        <p><strong>Status:</strong> <span style="background-color: #eee; padding: 3px 8px; border-radius: 4px;">{{ $activity->status }}</span></p>
    </div>

    @if ($activity->status === 'published')
        <form action="{{ route('activities.registrations.store', $activity) }}" method="POST">
            @csrf
            <label for="participant_name">Nama peserta</label>
            <input id="participant_name" name="participant_name" value="{{ old('participant_name') }}" required>
            @error('participant_name')
                <p style="color: red;">{{ $message }}</p>
            @enderror

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            @error('email')
                <p style="color: red;">{{ $message }}</p>
            @enderror

            <button type="submit">Daftar</button>
        </form>
    @endif

    <div>
        <p><strong>Deskripsi:</strong></p>
        <p>{{ $activity->description ?: 'Tidak ada deskripsi.' }}</p>
    </div>
@endsection
