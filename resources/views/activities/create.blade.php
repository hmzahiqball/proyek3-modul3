@extends('layouts.app')

@section('content')
    <h1>Tambah Kegiatan Baru</h1>

    <form action="{{ route('activities.store') }}" method="POST">
        @csrf
        @include('activities._form')

        <div style="margin-top: 20px;">
            <button type="submit">Simpan</button>
            <a href="{{ route('activities.index') }}">Batal</a>
        </div>
    </form>
@endsection
