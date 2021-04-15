@extends('layouts.app')

@section('content')
    <show-xml-component :details="{{ $details }}" />
@endsection