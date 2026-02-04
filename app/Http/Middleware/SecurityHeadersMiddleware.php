<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security headers that protect the users and the site
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        // CORS: Permitir comunicación entre diogenes.com.mx y www.diogenes.com.mx
        $origin = $request->headers->get('Origin');
        if ($origin && (str_contains($origin, 'diogenes.com.mx'))) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }

        // Content Security Policy (Flexibilidad total para activos locales en HTTPS)
        // Content Security Policy (Con soporte para YouTube y Media)
        $response->headers->set('Content-Security-Policy', "default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https: www.youtube.com s.ytimg.com www.youtube-nocookie.com; style-src 'self' 'unsafe-inline' https:; font-src 'self' data: https:; img-src 'self' data: https: i.ytimg.com youtube.com; frame-src 'self' https: www.youtube.com youtube.com www.youtube-nocookie.com; connect-src 'self' https: blob:; media-src 'self' https: blob: googlevideo.com *.googlevideo.com; worker-src 'self' blob:;");

        return $response;
    }
}
