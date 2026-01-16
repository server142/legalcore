<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .tenant-info {
            text-align: right;
        }
        .tenant-name {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 5px 10px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #4f46e5;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f9fafb;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    @if(isset($tenant->settings['logo_path']))
                        <img src="{{ storage_path('app/public/' . $tenant->settings['logo_path']) }}" class="logo">
                    @else
                        <span class="tenant-name">{{ $tenant->name }}</span>
                    @endif
                </td>
                <td class="tenant-info">
                    <div class="tenant-name">{{ $tenant->name }}</div>
                    <div>{{ $tenant->settings['direccion'] ?? '' }}</div>
                    <div>Titular: {{ $tenant->settings['titular'] ?? '' }}</div>
                </td>
            </tr>
        </table>
    </div>

    @yield('content')

    <div class="footer">
        Generado por Despacho Judicial - {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
