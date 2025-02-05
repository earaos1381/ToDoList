@extends('layouts.master')
@section('titulo' , 'Usuarios  :: SISTEMA DE TODO LIST')

@section('content')
    <div id="app">
        <users-vue :roles-granted="{{ json_encode($rolesGranted) }}" :permisos-granted="{{ json_encode($permisosGranted) }}" />
    </div>
@endsection
