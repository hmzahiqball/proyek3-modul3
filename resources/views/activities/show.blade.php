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
        <p><strong>Status:</strong> <span style="background-color: #eee; padding: 3px 8px; border-radius: 4px;">{{ $activity->status }}</span></p>
    </div>

    <div>
        <p><strong>Deskripsi:</strong></p>
        <p>{{ $activity->description ?: 'Tidak ada deskripsi.' }}</p>
    </div>
@endsection
