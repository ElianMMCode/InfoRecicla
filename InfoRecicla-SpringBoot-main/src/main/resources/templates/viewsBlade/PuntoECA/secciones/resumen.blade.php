@extends('PuntoECA.layouts.eca-layout')

@section('content')
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-muted small">Inventario total (kg)</div>
                    <div class="h4 mb-0" id="kpiInventario">{{ number_format($resumen['inventario_total'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-muted small">Entradas mes (kg)</div>
                    <div class="h4 mb-0" id="kpiEntradasMes">{{ number_format($resumen['entradas_mes'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-muted small">Salidas mes (kg)</div>
                    <div class="h4 mb-0" id="kpiSalidasMes">{{ number_format($resumen['salidas_mes'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-muted small">Próximo despacho</div>
                    <div class="h6 mb-0" id="kpiProximoDespacho">{{ $resumen['proximo_despacho'] ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-hover mt-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Alertas de capacidad / umbrales</strong>
            <span class="badge badge-soft" id="alertCount">{{ count($resumen['alertas']) }}</span>
        </div>
        <div class="card-body">
            <div id="alertList" class="vstack gap-2 small text-muted">
                @if (count($resumen['alertas']) > 0)
                    @foreach ($resumen['alertas'] as $alerta)
                        <div class="alert alert-{{ $alerta['tipo'] }} mb-0">
                            {{ $alerta['mensaje'] }}
                        </div>
                    @endforeach
                @else
                    Sin alertas.
                @endif
            </div>
        </div>
    </div>
@endsection
