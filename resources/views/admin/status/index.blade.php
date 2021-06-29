@extends('layouts.app')

@section('content')
    <status-component :status="{{ $status }}" />
@endsection