@extends('layouts.app')

@section('content')
    <statistics-component :data="{{ $collection }}" />
@endsection