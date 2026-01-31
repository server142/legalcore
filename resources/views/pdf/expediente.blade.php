@extends('pdf.layout')

@section('title', 'Reporte de Expediente ' . $expediente->numero)

@section('content')
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin: 0; color: #111827;">INFORME DE EXPEDIENTE</h1>
        <p style="color: #6b7280;">Número: {{ $expediente->numero }}</p>
    </div>

    <div class="section-title">DATOS GENERALES</div>
    <table class="data-table">
        <tr>
            <th style="width: 25%;">Título / Carátula:</th>
            <td>{{ $expediente->titulo }}</td>
        </tr>
        <tr>
            <th>Materia:</th>
            <td>{{ $expediente->materia }}</td>
        </tr>
        <tr>
            <th>Juzgado:</th>
            <td>{{ $expediente->juzgado }}</td>
        </tr>
        <tr>
            <th>Juez:</th>
            <td>{{ $expediente->nombre_juez ?? 'No asignado' }}</td>
        </tr>
        <tr>
            <th>Estado Procesal:</th>
            <td>{{ $expediente->estadoProcesal?->nombre ?? $expediente->estado_procesal }}</td>
        </tr>
        <tr>
            <th>Fecha Inicio:</th>
            <td>{{ $expediente->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="font-bold">CLIENTE:</div>
                <div>{{ $expediente->cliente->nombre }}</div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="font-bold">ABOGADO RESPONSABLE:</div>
                <div>{{ $expediente->abogado->name }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">ACTUACIONES ({{ $expediente->actuaciones->count() }})</div>
    @if($expediente->actuaciones->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Fecha</th>
                    <th>Actuación</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expediente->actuaciones as $actuacion)
                    <tr>
                        <td>{{ $actuacion->fecha?->format('d/m/Y') }}</td>
                        <td>{{ $actuacion->titulo }}</td>
                        <td>{{ $actuacion->descripcion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay actuaciones registradas.</p>
    @endif

    <div class="section-title">AGENDA Y CITAS ({{ $expediente->eventos->count() }})</div>
    @if($expediente->eventos->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Fecha</th>
                    <th>Evento</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expediente->eventos as $evento)
                    <tr>
                        <td>{{ $evento->start_time->format('d/m/Y H:i') }}</td>
                        <td>{{ $evento->titulo }}</td>
                        <td>{{ ucfirst($evento->tipo) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay eventos programados.</p>
    @endif

    <div class="section-title">RESUMEN DE DOCUMENTOS</div>
    <p>Este expediente cuenta con <strong>{{ $expediente->documentos->count() }}</strong> documentos anexos en el sistema digital.</p>

    @can('manage billing')
    <div class="section-title">ESTADO FINANCIERO</div>
    
    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td style="width: 33%; padding: 10px; background-color: #f3f4f6; border-radius: 5px;">
                <div style="font-size: 10px; color: #4b5563; font-weight: bold; text-transform: uppercase;">Honorarios Totales</div>
                <div style="font-size: 14px; color: #1f2937; font-weight: bold;">${{ number_format($expediente->honorarios_totales, 2) }}</div>
            </td>
            <td style="width: 33%; padding: 10px; background-color: #ecfdf5; border-radius: 5px; margin-left: 5px;">
                <div style="font-size: 10px; color: #059669; font-weight: bold; text-transform: uppercase;">Pagado / Abonado</div>
                <div style="font-size: 14px; color: #065f46; font-weight: bold;">
                    ${{ number_format($expediente->honorarios_totales - $expediente->saldo_pendiente, 2) }}
                </div>
            </td>
            <td style="width: 33%; padding: 10px; background-color: {{ $expediente->saldo_pendiente > 0 ? '#fef2f2' : '#f9fafb' }}; border-radius: 5px; margin-left: 5px;">
                <div style="font-size: 10px; color: {{ $expediente->saldo_pendiente > 0 ? '#dc2626' : '#6b7280' }}; font-weight: bold; text-transform: uppercase;">Saldo Pendiente</div>
                <div style="font-size: 14px; color: {{ $expediente->saldo_pendiente > 0 ? '#991b1b' : '#374151' }}; font-weight: bold;">
                    ${{ number_format($expediente->saldo_pendiente, 2) }}
                </div>
            </td>
        </tr>
    </table>

    @if($expediente->facturas->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expediente->facturas as $factura)
                    <tr>
                        <td>{{ $factura->created_at->format('d/m/Y') }}</td>
                        <td>{{ data_get($factura->conceptos, '0.descripcion', 'General') }}</td>
                        <td>
                             <span style="font-size: 10px; padding: 2px 6px; border-radius: 10px; background-color: {{ $factura->estado == 'pagada' ? '#d1fae5' : '#fee2e2' }}; color: {{ $factura->estado == 'pagada' ? '#065f46' : '#991b1b' }};">
                                {{ ucfirst($factura->estado) }}
                            </span>
                        </td>
                        <td style="text-align: right;">${{ number_format($factura->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #6b7280; font-size: 12px; font-style: italic;">No hay facturas o recibos registrados.</p>
    @endif
    @endcan


@endsection
