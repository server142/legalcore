<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Únete al Directorio Legal · Diogenes</title>
    <meta name="description" content="Posiciona tu práctica legal en la red de abogados verificados más confiable de México. Aumenta tu visibilidad y conecta con clientes.">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="font-family: 'Figtree', system-ui, sans-serif; background: #0c0f1a; color: white; margin: 0;">

<div class="min-h-screen relative overflow-hidden">

    {{-- Background Gradients --}}
    <div style="position:absolute;inset:0;pointer-events:none;z-index:0;">
        <div style="position:absolute;top:-10%;left:-10%;width:50%;height:50%;background:radial-gradient(ellipse, rgba(99,102,241,0.25), transparent 70%);"></div>
        <div style="position:absolute;bottom:-10%;right:-10%;width:50%;height:50%;background:radial-gradient(ellipse, rgba(139,92,246,0.25), transparent 70%);"></div>
        <div style="position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);background-size:28px 28px;"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-6 sm:px-8 py-16">

        {{-- Nav --}}
        <div class="flex items-center justify-between mb-16">
            <a href="{{ route('directory.public') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold transition-colors"
               style="color: #818cf8;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al Directorio
            </a>
            <a href="{{ route('login') }}"
               class="text-sm font-medium transition-colors"
               style="color: #94a3b8;">
                Ya tengo cuenta →
            </a>
        </div>

        {{-- Hero / Badge --}}
        <div class="text-center mb-20">
            <div class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-full mb-6 tracking-wider uppercase"
                 style="background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.35); color: #a5b4fc;">
                <span style="width:8px;height:8px;background:#34d399;border-radius:50%;display:inline-block;animation:pulse 2s infinite;"></span>
                Red de abogados verificados · México
            </div>
            <h1 class="font-black tracking-tight mb-6 leading-tight"
                style="font-size: clamp(2.5rem, 6vw, 5rem); background: linear-gradient(135deg, #fff 30%, #c7d2fe 60%, #c4b5fd 90%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">
                Posiciona tu<br>Práctica Legal
            </h1>
            <p class="text-xl max-w-2xl mx-auto font-light leading-relaxed" style="color: #94a3b8;">
                Únete a la red de abogados verificados más confiable de México. Aumenta tu visibilidad y conecta directamente con clientes que buscan tu especialidad.
            </p>
        </div>

        {{-- Features Grid --}}
        <div class="grid md:grid-cols-3 gap-6 mb-20">
            @php
                $features = [
                    ['num' => '1', 'bg' => 'rgba(99,102,241,0.18)',  'color' => '#a5b4fc', 'title' => 'Perfil Profesional Verificado',     'desc' => 'Destaca con insignia de verificación, foto profesional, bio, especialidades y enlaces en una ficha digital impecable.'],
                    ['num' => '2', 'bg' => 'rgba(139,92,246,0.18)',  'color' => '#c4b5fd', 'title' => 'Contacto Directo sin Intermediarios', 'desc' => 'Botón de WhatsApp integrado. El cliente llega a ti directamente, sin filtros ni comisiones de ningún tipo.'],
                    ['num' => '3', 'bg' => 'rgba(56,189,248,0.18)',  'color' => '#7dd3fc', 'title' => 'Dashboard de Estadísticas',          'desc' => 'Aparece en búsquedas por especialidad y ciudad. Monitorea visitas, impresiones y contactos desde tu panel.'],
                ];
            @endphp
            @foreach($features as $f)
            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 2rem; transition: all .3s; cursor: default;"
                 onmouseover="this.style.background='rgba(255,255,255,0.07)'; this.style.borderColor='rgba(255,255,255,0.15)'"
                 onmouseout="this.style.background='rgba(255,255,255,0.04)'; this.style.borderColor='rgba(255,255,255,0.08)'">
                <div style="width:56px;height:56px;background:{{ $f['bg'] }};border-radius:16px;display:flex;align-items:center;justify-content:center;color:{{ $f['color'] }};font-size:1.5rem;font-weight:900;margin-bottom:1.5rem;">
                    {{ $f['num'] }}
                </div>
                <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:.75rem;color:#f1f5f9;">{{ $f['title'] }}</h3>
                <p style="color:#64748b;line-height:1.7;font-size:.875rem;">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Stats Bar --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(255,255,255,0.08);border-radius:16px;overflow:hidden;margin-bottom:5rem;">
            @foreach([['500+', 'Abogados registrados'], ['15,000+', 'Búsquedas al mes'], ['98%', 'Clientes satisfechos']] as $s)
            <div style="background:#0c0f1a;padding:1.5rem 2rem;text-align:center;">
                <p style="font-size:2rem;font-weight:900;color:#fff;margin-bottom:.25rem;">{{ $s[0] }}</p>
                <p style="font-size:.75rem;color:#64748b;font-weight:500;">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>

        {{-- CTA Section --}}
        <div style="background:linear-gradient(135deg,rgba(30,27,75,0.8),rgba(49,46,129,0.6));border:1px solid rgba(255,255,255,0.12);border-radius:28px;padding:4rem 3rem;text-align:center;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,0.03) 1px,transparent 1px);background-size:24px 24px;pointer-events:none;"></div>
            <div style="position:relative;z-index:1;">
                <h2 style="font-size:2rem;font-weight:900;color:#fff;margin-bottom:1rem;">¿Listo para expandir tu alcance?</h2>
                <p style="color:#94a3b8;margin-bottom:2.5rem;max-width:28rem;margin-left:auto;margin-right:auto;line-height:1.7;">
                    Crea tu cuenta gratis y configura tu perfil público en menos de 5 minutos. Sin tarjeta requerida.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;margin-bottom:1.5rem;">
                    <a href="{{ route('register', ['plan' => 'directory-free']) }}"
                       style="display:inline-flex;align-items:center;gap:.5rem;padding:.875rem 2rem;background:#fff;color:#1e1b4b;border-radius:12px;font-weight:900;font-size:.95rem;text-decoration:none;transition:all .2s;box-shadow:0 8px 24px rgba(0,0,0,0.3);"
                       onmouseover="this.style.background='#e0e7ff'"
                       onmouseout="this.style.background='#fff'">
                        <svg style="width:18px;height:18px;color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Crear Cuenta Gratuita
                    </a>
                    <a href="{{ route('login') }}"
                       style="display:inline-flex;align-items:center;gap:.5rem;padding:.875rem 2rem;background:transparent;border:1px solid rgba(255,255,255,0.25);color:#e2e8f0;border-radius:12px;font-weight:600;font-size:.95rem;text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.08)'"
                       onmouseout="this.style.background='transparent'">
                        Ya tengo cuenta →
                    </a>
                </div>
                <p style="font-size:.75rem;color:#475569;">Sin compromisos · Cancela cuando quieras · Plan gratuito disponible</p>
            </div>
        </div>

        {{-- Footer --}}
        <div style="margin-top:4rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,0.06);text-align:center;color:#334155;font-size:.75rem;">
            © {{ date('Y') }} Diogenes Legal Core. Todos los derechos reservados.
        </div>

    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
</style>
</body>
</html>
