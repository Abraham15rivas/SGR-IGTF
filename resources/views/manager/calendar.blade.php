@extends('layouts.app')

@section('content')
    <calendar-component :dates="{{ $dates }}" />
@endsection