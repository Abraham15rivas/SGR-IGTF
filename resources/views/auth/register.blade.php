@extends('adminlte::auth.login')
@section('js')
    <script>
        window.location.href = '/login'
    </script>
@stop   
{{-- Views de registros deshabilitada --}}
{{-- @extends('adminlte::auth.register') --}}