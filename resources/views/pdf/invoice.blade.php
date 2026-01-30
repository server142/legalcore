@extends('pdf.layout')

@section('title', 'Factura #' . $factura->id)

@section('content')
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin: 0; color: #111827;">RECIBO DE DINERO</h1>
        <p style="color: #6b7280;">Folio: #{{ str_pad($factura->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="font-bold" style="margin-bottom: 5px;">CLIENTE:</div>
                <div>{{ $factura->cliente->nombre }}</div>
                <div>{{ $factura->cliente->email ?? '' }}</div>
                <div>{{ $factura->cliente->telefono ?? '' }}</div>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <div class="font-bold" style="margin-bottom: 5px;">FECHA DE EMISIÓN:</div>
                <div>{{ $factura->fecha_emision ? $factura->fecha_emision->format('d/m/Y') : now()->format('d/m/Y') }}</div>
                <div class="font-bold" style="margin-top: 10px; margin-bottom: 5px;">FECHA DE VENCIMIENTO:</div>
                <div>{{ $factura->fecha_vencimiento ? $factura->fecha_vencimiento->format('d/m/Y') : now()->addDays(30)->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="text-right" style="width: 100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($factura->conceptos && is_array($factura->conceptos))
                @foreach($factura->conceptos as $concepto)
                    <tr>
                        <td>{{ $concepto['descripcion'] ?? 'Servicios Legales' }}</td>
                        <td class="text-right">${{ number_format($concepto['monto'] ?? $factura->total, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>Servicios Legales Profesionales</td>
                    <td class="text-right">${{ number_format($factura->total, 2) }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right font-bold">SUBTOTAL:</td>
                <td class="text-right">${{ number_format($factura->subtotal, 2) }}</td>
            </tr>
            @if(isset($factura->iva) && $factura->iva > 0)
            <tr>
                <td class="text-right font-bold">IVA (16%):</td>
                <td class="text-right">${{ number_format($factura->iva, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="text-right font-bold">TOTAL:</td>
                <td class="text-right font-bold" style="font-size: 14px; color: #4f46e5;">
                    ${{ number_format($factura->total, 2) }} {{ $factura->moneda }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px;">
        <div class="font-bold" style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;">NOTAS:</div>
        <div style="font-size: 10px; color: #666;">
            {{ $tenant->settings['datos_generales'] ?? 'Gracias por su confianza.' }}
        </div>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #999; font-style: italic;">
        ESTE NO ES UN COMPROBANTE FISCAL VÁLIDO
    </div>

    <div style="margin-top: 50px; text-align: center;">
        <div style="width: 200px; border-top: 1px solid #000; margin: 0 auto; padding-top: 5px;">
            Firma Autorizada
        </div>
    </div>
@endsection
