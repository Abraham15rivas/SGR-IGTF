@extends('layouts.app')

@section('content')
    <user-activity-component :logs="{{ $logs }}" />
@endsection