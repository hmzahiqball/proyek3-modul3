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
        
        <form action="{{ route('activities.destroy', $activity) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" style="color: red;">Hapus</button>
        </form>
    </div>

    <h1>{{ $activity->title }}</h1>
    
    <div>
        <p><strong>Tanggal:</strong> {{ $activity->activity_date->format('d M Y') }}</p>
        <p><strong>Kategori:</strong> {{ $activity->category }}</p>
        <p><strong>Status:</strong> {{ $activity->status }}</p>
    </div>

    <div>
        <p><strong>Deskripsi:</strong></p>
        <p>{{ $activity->description ?: 'Tidak ada deskripsi.' }}</p>
    </div>
@endsection
