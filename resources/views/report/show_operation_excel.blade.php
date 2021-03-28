@extends('layouts.app')

@section('content')
    <show-operation-component :transactions="{{ $transactions }}" />
@endsection