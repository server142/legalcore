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

    @if(is_array($tenant->settings) && isset($tenant->settings['titulares_adjuntos']))
        <div style="margin-top: 30px;">
            <div class="font-bold">Titulares Adjuntos:</div>
            <div style="font-size: 11px;">{{ $tenant->settings['titulares_adjuntos'] }}</div>
        </div>
    @endif
@endsection
