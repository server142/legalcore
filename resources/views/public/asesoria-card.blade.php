<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprobante de Cita - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-xl">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-white/80">Cita de Asesoría</div>
                            <div class="mt-1 text-2xl font-extrabold">{{ $asesoria->folio }}</div>
                            <div class="mt-2 text-sm text-white/90">Comprobante de programación</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold uppercase tracking-wider text-white/80">Estado</div>
                            <div class="mt-1 inline-flex items-center px-3 py-1 rounded-full bg-white/15 border border-white/20 text-sm font-bold">
                                {{ strtoupper(str_replace('_', ' ', $asesoria->estado)) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                            <div class="text-xs font-bold text-gray-500 uppercase">Cliente</div>
                            <div class="mt-1 text-sm font-bold text-gray-900">
                                {{ $asesoria->cliente->nombre ?? $asesoria->nombre_prospecto ?? '—' }}
                            </div>
                            @if(!empty($asesoria->telefono))
                                <div class="mt-1 text-xs text-gray-600">{{ $asesoria->telefono }}</div>
                            @endif
                            @if(!empty($asesoria->email))
                                <div class="mt-1 text-xs text-gray-600">{{ $asesoria->email }}</div>
                            @endif
                        </div>

                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                            <div class="text-xs font-bold text-gray-500 uppercase">Abogado</div>
                            <div class="mt-1 text-sm font-bold text-gray-900">
                                {{ $asesoria->abogado->name ?? '—' }}
                            </div>
                            <div class="mt-2 text-xs text-gray-600">Duración: {{ (int) $asesoria->duracion_minutos }} min</div>
                            <div class="mt-1 text-xs text-gray-600">Tipo: {{ ucfirst($asesoria->tipo) }}</div>
                        </div>

                        <div class="p-4 rounded-xl border border-gray-200 bg-white sm:col-span-2">
                            <div class="text-xs font-bold text-gray-500 uppercase">Fecha y hora</div>
                            <div class="mt-1 text-lg font-extrabold text-gray-900">
                                {{ optional($asesoria->fecha_hora)->format('d/m/Y H:i') ?? '—' }}
                            </div>
                            @if($asesoria->tipo === 'presencial')
                                <div class="mt-2 text-xs text-gray-600">Llega 10 minutos antes y considera tiempo para estacionarte y registrarte.</div>
                            @elseif($asesoria->tipo === 'videoconferencia')
                                <div class="mt-2 text-xs text-gray-600">Conéctate 5 minutos antes, prueba tu cámara/micrófono y entra desde una red estable.</div>
                            @else
                                <div class="mt-2 text-xs text-gray-600">Ten tu teléfono con buena señal y contesta llamadas desconocidas 5 minutos antes de la hora.</div>
                            @endif
                        </div>

                        <div class="p-4 rounded-xl border border-gray-200 bg-white sm:col-span-2">
                            <div class="text-xs font-bold text-gray-500 uppercase">Asunto</div>
                            <div class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $asesoria->asunto }}</div>
                        </div>

                        @if(!empty($asesoria->link_videoconferencia) && $asesoria->tipo === 'videoconferencia')
                            <div class="p-4 rounded-xl border border-indigo-100 bg-indigo-50 sm:col-span-2">
                                <div class="text-xs font-bold text-indigo-700 uppercase">Link de videoconferencia</div>
                                <a class="mt-2 block text-sm font-bold text-indigo-800 underline break-all" href="{{ $asesoria->link_videoconferencia }}" target="_blank" rel="noopener">{{ $asesoria->link_videoconferencia }}</a>
                            </div>
                        @endif

                        @php
                            $settings = $tenant->settings ?? [];
                            $contactPhone = trim((string) ($settings['asesorias_contact_phone'] ?? ''));
                            $direccion = trim((string) ($settings['direccion'] ?? ''));
                            $mapsUrl = !empty($direccion)
                                ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($direccion)
                                : null;

                            $qrUrl = null;
                            $qrTitle = null;
                            $qrHint = null;

                            if ($asesoria->tipo === 'videoconferencia' && !empty($asesoria->link_videoconferencia)) {
                                $qrUrl = $asesoria->link_videoconferencia;
                                $qrTitle = 'Escanea para abrir la videollamada';
                                $qrHint = 'Recomendación: usa audífonos y una red estable.';
                                
                                // Si la URL es muy larga, acortarla para QR
                                if (strlen($qrUrl) > 200) {
                                    // Extraer solo el dominio y path principal para QR
                                    $parsed = parse_url($qrUrl);
                                    $qrUrl = $parsed['scheme'] . '://' . $parsed['host'] . (isset($parsed['path']) ? $parsed['path'] : '');
                                    if (isset($parsed['query'])) {
                                        $qrUrl .= '?' . $parsed['query'];
                                    }
                                }
                            } elseif ($asesoria->tipo === 'telefonica' && !empty($contactPhone)) {
                                $phoneDigits = preg_replace('/\D+/', '', $contactPhone);
                                $qrUrl = 'tel:+' . ltrim($phoneDigits, '+');
                                $qrTitle = 'Escanea para llamar al despacho';
                                $qrHint = 'Si tu equipo no abre llamadas desde QR, copia el número y márcalo manualmente.';
                            } elseif ($asesoria->tipo === 'presencial' && !empty($direccion)) {
                                $qrUrl = $mapsUrl;
                                $qrTitle = 'Escanea para abrir la ubicación';
                                $qrHint = 'Planea tu ruta con tiempo (tráfico/estacionamiento).';
                            }

                            $qrImg = $qrUrl ? true : null;
                            $lat = $settings['asesorias_location_lat'] ?? null;
                            $lon = $settings['asesorias_location_lon'] ?? null;
                        @endphp

                        @if($qrImg)
                            <div class="p-4 rounded-xl border border-gray-200 bg-white sm:col-span-2">
                                <div class="flex flex-col sm:flex-row gap-4 items-start">
                                    <div class="shrink-0">
                                        <div id="qr" class="w-[180px] h-[180px] rounded-xl border border-gray-200 bg-white flex items-center justify-center"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-bold text-gray-500 uppercase">QR</div>
                                        <div class="mt-1 text-sm font-extrabold text-gray-900">{{ $qrTitle }}</div>
                                        <div class="mt-2 text-xs text-gray-600">{{ $qrHint }}</div>

                                        <div class="mt-3">
                                            <div class="text-xs font-bold text-gray-500 uppercase">Instrucciones</div>
                                            <div class="mt-1 text-xs text-gray-700">
                                                1) Abre la cámara o tu app de QR.
                                                2) Escanea el código.
                                                3) Toca la notificación para abrir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($asesoria->tipo === 'presencial')
                            <div class="p-4 rounded-xl border border-amber-100 bg-amber-50 sm:col-span-2">
                                <div class="text-xs font-bold text-amber-700 uppercase">Ubicación del despacho</div>

                                @if(!empty($direccion))
                                    <div class="mt-2 text-sm font-bold text-amber-900 whitespace-pre-line">{{ $direccion }}</div>
                                @else
                                    <div class="mt-2 text-sm text-amber-900">Dirección no configurada.</div>
                                @endif

                                @if(!empty($contactPhone))
                                    <div class="mt-2 text-sm text-amber-900">
                                        <span class="font-bold">Teléfono:</span>
                                        <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}" class="underline font-bold">{{ $contactPhone }}</a>
                                    </div>
                                @endif

                                @if(!empty($mapsUrl))
                                    <div class="mt-3">
                                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-amber-600 text-white font-extrabold hover:bg-amber-700">
                                            Abrir en Google Maps
                                        </a>
                                    </div>
                                @endif

                                <div class="mt-3 text-xs text-amber-800">
                                    Recomendación: llega con anticipación y considera tráfico/estacionamiento.
                                </div>
                            </div>
                        @endif

                        @php
                            $tBank = trim((string) ($settings['payment_transfer_bank'] ?? ''));
                            $tHolder = trim((string) ($settings['payment_transfer_holder'] ?? ''));
                            $tClabe = trim((string) ($settings['payment_transfer_clabe'] ?? ''));
                            $tAccount = trim((string) ($settings['payment_transfer_account'] ?? ''));
                            $cBank = trim((string) ($settings['payment_card_bank'] ?? ''));
                            $cHolder = trim((string) ($settings['payment_card_holder'] ?? ''));
                            $cNumber = trim((string) ($settings['payment_card_number'] ?? ''));

                            $hasTransfer = !empty($tBank) || !empty($tHolder) || !empty($tClabe) || !empty($tAccount);
                            $hasCard = !empty($cBank) || !empty($cHolder) || !empty($cNumber);
                        @endphp

                        @if($hasTransfer || $hasCard)
                            <div class="p-4 rounded-xl border border-emerald-100 bg-emerald-50 sm:col-span-2">
                                <div class="text-xs font-bold text-emerald-700 uppercase">Formas de pago</div>
                                <div class="mt-2 text-xs text-emerald-800">Si deseas pagar antes de la cita, puedes usar cualquiera de estas opciones:</div>

                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @if($hasTransfer)
                                        <div class="bg-white rounded-xl border border-emerald-100 p-4">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                                                <div class="text-sm font-extrabold text-emerald-900">Transferencia</div>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-700 space-y-1">
                                                @if($tBank)<div><span class="font-bold">Banco:</span> {{ $tBank }}</div>@endif
                                                @if($tHolder)<div><span class="font-bold">Titular:</span> {{ $tHolder }}</div>@endif
                                                @if($tClabe)<div><span class="font-bold">CLABE:</span> {{ $tClabe }}</div>@endif
                                                @if($tAccount)<div><span class="font-bold">Cuenta:</span> {{ $tAccount }}</div>@endif
                                            </div>
                                        </div>
                                    @endif

                                    @if($hasCard)
                                        <div class="bg-white rounded-xl border border-emerald-100 p-4">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 11h18M7 15h4m-7 4h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <div class="text-sm font-extrabold text-emerald-900">Tarjeta</div>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-700 space-y-1">
                                                @if($cBank)<div><span class="font-bold">Banco:</span> {{ $cBank }}</div>@endif
                                                @if($cHolder)<div><span class="font-bold">Titular:</span> {{ $cHolder }}</div>@endif
                                                @if($cNumber)<div><span class="font-bold">Tarjeta:</span> {{ $cNumber }}</div>@endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(!empty($lat) && !empty($lon) && $asesoria->fecha_hora)
                            <div class="p-4 rounded-xl border border-sky-100 bg-sky-50 sm:col-span-2">
                                <div class="text-xs font-bold text-sky-700 uppercase">Clima (aprox.)</div>
                                <div id="weather" class="mt-2 text-sm text-sky-900 font-bold">Cargando clima...</div>
                                <div class="mt-1 text-xs text-sky-700">Información de Open-Meteo (sin clave). Puede variar.</div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        @php
                            $msg = "Hola, aquí está tu comprobante de cita de asesoría ({$asesoria->folio}).";
                            $wa = null;
                            if (!empty($asesoria->telefono)) {
                                $phone = preg_replace('/\D+/', '', $asesoria->telefono);
                                $wa = "https://wa.me/{$phone}?text=" . urlencode($msg . ' ' . request()->fullUrl());
                            }
                        @endphp

                        @if($wa)
                            <a href="{{ $wa }}" target="_blank" rel="noopener" class="w-full sm:w-auto text-center px-4 py-3 rounded-xl bg-green-600 text-white font-extrabold hover:bg-green-700">
                                Enviar por WhatsApp
                            </a>
                        @endif

                        <button type="button" onclick="navigator.clipboard.writeText(window.location.href)" class="w-full sm:w-auto px-4 py-3 rounded-xl bg-gray-900 text-white font-extrabold hover:bg-gray-800">
                            Copiar enlace
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="text-xs text-gray-500">
                        Este comprobante es informativo. Si necesitas reprogramar, responde a este mensaje.
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-500">
                {{ config('app.name') }}
            </div>
        </div>
    </div>

    @if(!empty($lat) && !empty($lon) && $asesoria->fecha_hora)
        <script>
            (function () {
                const el = document.getElementById('weather');
                if (!el) return;

                const lat = @json((float) $lat);
                const lon = @json((float) $lon);
                const iso = @json($asesoria->fecha_hora->format('Y-m-d\\TH:00:00'));
                const date = @json($asesoria->fecha_hora->format('Y-m-d'));

                const url = `https://api.open-meteo.com/v1/forecast?latitude=${encodeURIComponent(lat)}&longitude=${encodeURIComponent(lon)}&hourly=temperature_2m,precipitation_probability,weathercode&timezone=auto&start_date=${date}&end_date=${date}`;

                fetch(url)
                    .then(r => r.json())
                    .then(data => {
                        const t = data?.hourly?.time || [];
                        const temp = data?.hourly?.temperature_2m || [];
                        const pop = data?.hourly?.precipitation_probability || [];

                        const idx = t.indexOf(iso);
                        if (idx === -1) {
                            el.textContent = 'No disponible para la hora exacta.';
                            return;
                        }

                        const vTemp = temp[idx];
                        const vPop = pop[idx];
                        el.textContent = `${vTemp}°C · Prob. lluvia ${vPop}% (hora de la cita)`;
                    })
                    .catch(() => {
                        el.textContent = 'No se pudo cargar el clima.';
                    });
            })();
        </script>
    @endif

    @if(!empty($qrUrl))
        <script src="/js/qrcode.js"></script>
        <script>
            (function () {
                const el = document.getElementById('qr');
                if (!el) return;

                // Hardcodear URL corta para pruebas
                    const url = 'https://meet.google.com/abc-defg-hij'; // URL de prueba corta
                    console.log('QR URL (hardcodeada):', url); // Debug
                    
                    if (typeof window.qrcode !== 'function') {
                        console.error('QR library not loaded');
                        el.innerHTML = '<div class="text-xs text-gray-500">QR no disponible</div>';
                        return;
                    }

                    // Múltiples fallbacks para encontrar configuración válida
                    const configs = [
                        { type: 5, level: 'L' },
                        { type: 4, level: 'L' },
                        { type: 3, level: 'L' },
                        { type: 2, level: 'L' },
                        { type: 1, level: 'L' }
                    ];
                    
                    let qr = null;
                    let error = null;
                    
                    for (const config of configs) {
                        try {
                            qr = window.qrcode(config.type, config.level);
                            qr.addData(url);
                            qr.make();
                            console.log('QR generado con:', config);
                            break;
                        } catch (e) {
                            console.log('Falló config', config, ':', e.message);
                            error = e;
                            continue;
                        }
                    }
                    
                    if (!qr) {
                        console.error('No se pudo generar QR con ninguna configuración:', error);
                        el.innerHTML = '<div class="text-xs text-gray-500">QR no disponible</div>';
                        return;
                    }
                    
                    // Generar SVG con mejor configuración
                    const svgString = qr.createSvgTag(4, 0); // cellSize=4, margin=0
                    el.innerHTML = svgString;
                    
                    // Asegurar que el SVG tenga tamaño correcto
                    const svg = el.querySelector('svg');
                    if (svg) {
                        svg.setAttribute('width', '180');
                        svg.setAttribute('height', '180');
                        svg.style.width = '180px';
                        svg.style.height = '180px';
                    }
                } catch (e) {
                    console.error('Error generando QR:', e);
                    el.innerHTML = '<div class="text-xs text-gray-500">QR no disponible</div>';
                }
            })();
        </script>
    @else
        <script>
            console.log('QR URL está vacío o nulo');
            console.log('Tipo asesoría:', @json($asesoria->tipo));
            console.log('Link videoconferencia:', @json($asesoria->link_videoconferencia ?? 'vacio'));
            console.log('Contact phone:', @json($contactPhone ?? 'vacio'));
            console.log('Dirección:', @json($direccion ?? 'vacio'));
        </script>
    @endif
</body>
</html>
