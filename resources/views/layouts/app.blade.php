@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="text-center">
        <h1>{{ isset($title) ? $title : 'Título'}}</h1>
    </div>
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