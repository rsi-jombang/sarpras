<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 20px 25px;
        }

        .page-header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .page-header .rs-name {
            font-size: 11px;
            font-weight: bold;
            color: #4f46e5;
        }

        .page-header .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
            word-break: break-word;
        }

        .page-header .report-meta {
            font-size: 10px;
            color: #64748b;
            margin-top: 4px;
        }

        .meta-grid {
            display: flex;
            gap: 24px;
            margin-bottom: 12px;
        }

        .meta-item {}

        .meta-item .label {
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-item .value {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }

        .summary-cards {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .summary-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }

        .summary-card .s-value {
            font-size: 20px;
            font-weight: bold;
            color: #4f46e5;
        }

        .summary-card .s-label {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        thead tr {
            background: #4f46e5;
            color: #fff;
        }

        thead th {
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody td {
            padding: 6px 8px;
            font-size: 10px;
            vertical-align: middle;
        }

        tfoot tr {
            background: #eef2ff;
        }

        tfoot td {
            padding: 7px 8px;
            font-size: 10px;
            font-weight: bold;
            border-top: 2px solid #4f46e5;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 99px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef9c3;
            color: #713f12;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #e0f2fe;
            color: #075985;
        }

        .badge-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #94a3b8;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-success {
            color: #16a34a;
        }

        .text-danger {
            color: #dc2626;
        }

        .text-primary {
            color: #4f46e5;
        }

        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding: 6px 0;
            background: #fff;
        }
    </style>
</head>

<body>

    <div class="page-header">
        <div class="rs-name">RUMAH SAKIT — SISTEM MANAJEMEN SARANA PRASARANA</div>
        <div class="report-title">@yield('title')</div>
        <div class="report-meta">Dicetak: {{ now()->translatedFormat('l, d F Y') }} pukul {{ now()->format('H:i') }} WIB
        </div>
    </div>

    @yield('content')

    <div class="page-footer">
        Laporan ini dicetak secara otomatis oleh Sistem Manajemen Sarpras &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>
