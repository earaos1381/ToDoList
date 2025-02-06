@extends('layouts.master')
@section('titulo' , 'Dashboard  :: SISTEMA DE TODO LIST')

@section('content')
    <div id="app">
        <dashboard-vue :roles-granted="{{ json_encode($rolesGranted) }}" :permisos-granted="{{ json_encode($permisosGranted) }}" />
    </div>
@endsection
