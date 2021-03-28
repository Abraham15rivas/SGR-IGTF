@extends('layouts.app')

@section('content')
    <show-temporary-component :temporary="{{ $temporary }}" />
@endsection