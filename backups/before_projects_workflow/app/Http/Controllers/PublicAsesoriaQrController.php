<?php

namespace App\Http\Controllers;

use App\Models\Asesoria;
use Illuminate\Http\Response;

class PublicAsesoriaQrController extends Controller
{
    public function show(string $token)
    {
        return response('QR migrado: ahora se genera dentro de la tarjeta (frontend).', 410);

        $asesoria = Asesoria::with(['tenant'])->where('public_token', $token)->firstOrFail();
        $tenant = $asesoria->tenant;
        $settings = $tenant?->settings ?? [];

        $contactPhone = trim((string) ($settings['asesorias_contact_phone'] ?? ''));
        $direccion = trim((string) ($settings['direccion'] ?? ''));

        $qrUrl = null;
        if ($asesoria->tipo === 'videoconferencia' && !empty($asesoria->link_videoconferencia)) {
            $qrUrl = $asesoria->link_videoconferencia;
        } elseif ($asesoria->tipo === 'telefonica' && !empty($contactPhone)) {
            $phoneDigits = preg_replace('/\D+/', '', $contactPhone);
            $qrUrl = 'tel:+' . ltrim($phoneDigits, '+');
        } elseif ($asesoria->tipo === 'presencial' && !empty($direccion)) {
            $qrUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($direccion);
        }

        if (empty($qrUrl)) {
            abort(404);
        }

        try {
            $builder = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\SvgWriter())
                ->data($qrUrl)
                ->size(220)
                ->margin(10)
                ->build();

            $svg = $builder->getString();

            return response($svg, 200)
                ->header('Content-Type', 'image/svg+xml; charset=UTF-8')
                ->header('Cache-Control', 'public, max-age=86400');
        } catch (\Throwable $e) {
            return response(
                'Error generando QR: ' . $e->getMessage()
                . "\n\nSi acabas de instalar dependencias, reinicia el servidor (php artisan serve) y ejecuta composer dump-autoload.",
                500
            );
        }
    }
}
