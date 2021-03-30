@extends('layouts.app')

@section('content')
    <user-list-component :users="{{ $users }}" />
@endsection