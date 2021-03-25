@extends('layouts.app')

@section('content')
    <show-core-component :transactions="{{ $transactions }}" />
@endsection