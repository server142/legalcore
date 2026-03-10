<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Diogenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.5.0/github-markdown.min.css">
    <style>
        .markdown-body {
            box-sizing: border-box;
            min-width: 200px;
            max-width: 980px;
            margin: 0 auto;
            padding: 45px;
        }
        @media (max-width: 767px) {
            .markdown-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">
                        Diogenes
                    </a>
                    <nav class="flex space-x-4">
                        <a href="{{ route('privacy') }}" class="text-gray-600 hover:text-indigo-600 {{ request()->routeIs('privacy') ? 'font-bold text-indigo-600' : '' }}">
                            Privacidad
                        </a>
                        <a href="{{ route('terms') }}" class="text-gray-600 hover:text-indigo-600 {{ request()->routeIs('terms') ? 'font-bold text-indigo-600' : '' }}">
                            Términos
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="py-12">
            <div class="markdown-body bg-white rounded-lg shadow-sm">
                <div class="mb-8 border-b pb-4">
                    <h1 class="!border-0 !mb-0">{{ $title }}</h1>
                    <div class="text-xs text-gray-500 mt-2">
                        Versión {{ $version ?? '1.0' }} | 
                        Última actualización: {{ isset($date) && $date ? $date->format('d/m/Y') : date('d/m/Y') }}
                    </div>
                </div>
                
                @if(str_contains($content, '<p>') || str_contains($content, '<div>'))
                    {!! $content !!}
                @else
                    {!! \Illuminate\Support\Str::markdown($content) !!}
                @endif
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-gray-500 text-sm">
                    © {{ date('Y') }} Diogenes. Todos los derechos reservados.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
