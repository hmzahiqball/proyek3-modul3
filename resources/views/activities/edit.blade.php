@extends('layouts.app')

@section('content')
    <h1>Edit Kegiatan</h1>

    <form action="{{ route('activities.update', $activity) }}" method="POST">
        @csrf
        @method('PUT')
        @include('activities._form')

        <div style="margin-top: 20px;">
            <button type="submit">Update</button>
            <a href="{{ route('activities.show', $activity) }}">Batal</a>
        </div>
    </form>
@endsection
