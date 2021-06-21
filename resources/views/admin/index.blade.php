@extends('layouts.app')

@section('content')
    <index-component :notifications="{{ $notifications }}" />
@endsection