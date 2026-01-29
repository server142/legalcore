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

    <div class="section-title">COMENTARIOS ({{ $expediente->comentarios->count() }})</div>
    @if($expediente->comentarios->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Fecha</th>
                    <th>Usuario</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expediente->comentarios->take(10) as $comentario)
                    <tr>
                        <td>{{ $comentario->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $comentario->user->name }}</td>
                        <td>{{ $comentario->contenido }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($expediente->comentarios->count() > 10)
            <p style="font-size: 11px; color: #6b7280; margin-top: 5px;">
                * Mostrando los últimos 10 comentarios de {{ $expediente->comentarios->count() }} totales
            </p>
        @endif
    @else
        <p>No hay comentarios registrados.</p>
    @endif

    <div class="section-title">RESUMEN FINANCIERO</div>
    @if($expediente->costo_total && $expediente->costo_total > 0)
        <table class="data-table">
            <tr>
                <th style="width: 35%;">Importe Total:</th>
                <td style="font-weight: bold; color: #111827;">${{ number_format($expediente->costo_total, 2) }}</td>
            </tr>
            <tr>
                <th>Total Abonado:</th>
                <td style="font-weight: bold; color: #059669;">${{ number_format($expediente->getTotalPagadoAttribute(), 2) }}</td>
            </tr>
            <tr>
                <th>Saldo Pendiente:</th>
                <td style="font-weight: bold; color: #d97706;">${{ number_format($expediente->saldo_pendiente, 2) }}</td>
            </tr>
            <tr>
                <th>Estado de Cobro:</th>
                <td>
                    @if($expediente->estado_cobro == 'liquidado')
                        <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-size: 11px;">LIQUIDADO</span>
                    @elseif($expediente->estado_cobro == 'parcial_pagado')
                        <span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 11px;">PARCIAL</span>
                    @else
                        <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 4px; font-size: 11px;">PENDIENTE</span>
                    @endif
                </td>
            </tr>
        </table>
        
        @if($expediente->pagos->count() > 0)
            <div style="margin-top: 15px;">
                <div style="font-weight: bold; margin-bottom: 8px;">Detalle de Pagos ({{ $expediente->pagos->count() }}):</div>
                <table class="data-table" style="font-size: 11px;">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Fecha</th>
                            <th style="width: 60px;">Tipo</th>
                            <th style="width: 80px;">Monto</th>
                            <th style="width: 80px;">Método</th>
                            <th>Referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expediente->pagos->sortBy('fecha_pago') as $pago)
                            <tr>
                                <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                <td style="text-align: center;">
                                    @if($pago->tipo_pago == 'anticipo')
                                        <span style="background: #fef3c7; color: #92400e; padding: 1px 4px; border-radius: 2px; font-size: 10px;">ANT</span>
                                    @else
                                        <span style="background: #dbeafe; color: #1e40af; padding: 1px 4px; border-radius: 2px; font-size: 10px;">PAR</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">${{ number_format($pago->monto, 2) }}</td>
                                <td>{{ $pago->metodo_pago }}</td>
                                <td>{{ $pago->referencia ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <p>Este expediente no tiene configurado un costo total.</p>
    @endif

    <div class="section-title">RESUMEN DE DOCUMENTOS</div>
    <p>Este expediente cuenta con <strong>{{ $expediente->documentos->count() }}</strong> documentos anexos en el sistema digital.</p>

    @if(is_array($tenant->settings) && isset($tenant->settings['titulares_adjuntos']))
        <div style="margin-top: 30px;">
            <div class="font-bold">Titulares Adjuntos:</div>
            <div style="font-size: 11px;">{{ $tenant->settings['titulares_adjuntos'] }}</div>
        </div>
    @endif
@endsection
