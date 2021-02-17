@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @php
        $route = 'Definir';
    @endphp
    <h1>Dashboard {{ $route }}</h1>
@stop

@section('content')
    @yield('content')
@stop

@section('css')
    <!-- Styles addicionales -->
@stop

@section('js')
    <!-- Scripts adicionales -->
@stop    