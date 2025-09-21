@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <!-- TABS DE TODAS LAS SECCIONES-->
        <ul class="nav nav-pills" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.resumen') }}" class="nav-link {{ $seccion === 'resumen' ? 'active' : '' }}">Resumen</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.perfil') }}" class="nav-link {{ $seccion === 'perfil' ? 'active' : '' }}">Perfil</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.materiales') }}"
                    class="nav-link {{ $seccion === 'materiales' ? 'active' : '' }}">Materiales</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.movimientos') }}"
                    class="nav-link {{ $seccion === 'movimientos' ? 'active' : '' }}">Movimientos</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.historial') }}"
                    class="nav-link {{ $seccion === 'historial' ? 'active' : '' }}">Historial</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.calendario') }}"
                    class="nav-link {{ $seccion === 'calendario' ? 'active' : '' }}">Calendario</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.centros') }}"
                    class="nav-link {{ $seccion === 'centros' ? 'active' : '' }}">Centros</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.conversaciones') }}"
                    class="nav-link {{ $seccion === 'conversaciones' ? 'active' : '' }}">Conversaciones</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('eca.configuracion') }}"
                    class="nav-link {{ $seccion === 'configuracion' ? 'active' : '' }}">Configuración</a>
            </li>
        </ul>

        <div class="tab-content pt-3">
            @yield('content')
        </div>
    </div>
@endsection
