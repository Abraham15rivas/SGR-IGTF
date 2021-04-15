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

@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2021-{{ date('Y') }} <a href="{{ config('app.url_footer', 'Laravel') }}" target="_blank">BAV</a>.</strong> All rights reserved.
@stop

@section('css')
    <!-- Styles addicionales -->
    <link rel="stylesheet" href="{{ asset('css/app2.css') }}">
@stop

@section('js')
    <!-- Scripts adicionales -->
@stop    