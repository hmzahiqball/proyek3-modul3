@extends('layouts.app')

@section('content')
    <h1>Kegiatan Terhapus</h1>

    @forelse ($activities as $activity)
        <article style="margin-bottom: 12px;">
            <strong>[{{ $activity->code }}] {{ $activity->title }}</strong>
            <span>Dihapus {{ $activity->deleted_at->format('d M Y H:i') }}</span>
            <form action="{{ route('activities.restore', $activity->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit">Pulihkan</button>
            </form>
        </article>
    @empty
        <p>Tidak ada kegiatan terhapus.</p>
    @endforelse

    <a href="{{ route('activities.index') }}">Kembali ke daftar aktif</a>
@endsection
