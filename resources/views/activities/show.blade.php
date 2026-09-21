@extends('layouts.app')

@section('content')
    <a href="{{ route('activities.index') }}">Kembali ke Daftar</a>
    
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
