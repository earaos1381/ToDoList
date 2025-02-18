@extends('layouts.master')
@section('titulo' , 'Roles  :: SISTEMA DE TODO LIST')

@section('content')
    <div id="app">
        <roles-vue :roles-granted="{{ json_encode($rolesGranted) }}" :permisos-granted="{{ json_encode($permisosGranted) }}" />
    </div>
@endsection
