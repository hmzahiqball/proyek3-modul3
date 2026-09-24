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

    <div style="margin-bottom: 20px;">
        <strong>Filter Status:</strong>
        <a href="{{ route('activities.index') }}" @if(!request()->has('status') || !in_array(request('status'), ['Planned', 'Ongoing', 'Done'])) style="font-weight: bold; text-decoration: underline;" @endif>Semua</a> |
        <a href="{{ route('activities.index', ['status' => 'Planned']) }}" @if(request('status') === 'Planned') style="font-weight: bold; text-decoration: underline;" @endif>Planned</a> |
        <a href="{{ route('activities.index', ['status' => 'Ongoing']) }}" @if(request('status') === 'Ongoing') style="font-weight: bold; text-decoration: underline;" @endif>Ongoing</a> |
        <a href="{{ route('activities.index', ['status' => 'Done']) }}" @if(request('status') === 'Done') style="font-weight: bold; text-decoration: underline;" @endif>Done</a>
    </div>

    @forelse ($activities as $activity)
        <article class="card">
            <h2>
                <a href="{{ route('activities.show', $activity) }}">
                    {{ $activity->title }}
                </a>
            </h2>
            <p>{{ $activity->activity_date->format('d M Y') }}</p>
            <p>Status: {{ $activity->status }}</p>
        </article>
    @empty
        <p>Belum ada kegiatan.</p>
    @endforelse
@endsection
