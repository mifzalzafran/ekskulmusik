<!-- TODO(UI) -->
@extends('layouts.app', ['title' => '403 Forbidden'])

@section('content')
    <h1>403 Forbidden</h1>
    <p>{{ $exception->getMessage() ?: 'You do not have permission to access this resource.' }}</p>
    <p><a href="{{ url('/') }}">Return Home</a></p>
@endsection
