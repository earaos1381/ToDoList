@extends('layouts.master')
@section('titulo' , 'Roles  :: SISTEMA DE TODO LIST')

@section('content')
    <div id="app">
        <roles-vue :roles="{{ json_encode($roles) }}" :permisos="{{ json_encode($permisos) }}" />
    </div>
@endsection
