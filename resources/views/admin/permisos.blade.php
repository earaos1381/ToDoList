@extends('layouts.master')
@section('titulo' , 'Permisos  :: SISTEMA DE TODO LIST')

@section('content')
    <div id="app">
        <permisos-vue :roles-granted="{{ json_encode($rolesGranted) }}" :permisos-granted="{{ json_encode($permisosGranted) }}" />
    </div>
@endsection
