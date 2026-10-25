<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Laporan INTRASOL')</title>
    <style>
        @page {
            margin: 18mm 14mm 22mm 14mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #171717;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #171717;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo-cell {
            width: 54px;
        }

        .logo {
            max-width: 44px;
            max-height: 44px;
        }

        .company {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.2px;
        }

        .company-sub {
            font-size: 9pt;
            color: #555555;
        }

        .report-title {
            font-size: 15pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .meta {
            font-size: 9pt;
            color: #444444;
            margin-bottom: 12px;
        }

        .meta span + span::before {
            content: "  |  ";
            color: #999999;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .summary td {
            border: 2px solid #171717;
            padding: 8px 10px;
            width: 33.33%;
            vertical-align: top;
        }

        .summary .label {
            font-size: 8pt;
            color: #555555;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .summary .value {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 2px;
        }

        .positive {
            color: #166534;
        }

        .negative {
            color: #B42318;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.data thead {
            display: table-header-group;
        }

        table.data th {
            background: #F5E64D;
            border: 1px solid #171717;
            padding: 6px;
            font-size: 8.5pt;
            text-align: left;
            vertical-align: bottom;
        }

        table.data td {
            border-bottom: 1px solid #DDDDDD;
            padding: 5px 6px;
            font-size: 8.5pt;
            vertical-align: top;
        }

        table.data tr {
            page-break-inside: avoid;
        }

        table.data tfoot td {
            border-top: 2px solid #171717;
            border-bottom: 2px solid #171717;
            font-weight: bold;
            background: #F7F5EF;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #666666;
        }

        .badge {
            display: inline-block;
            border: 1px solid #171717;
            padding: 1px 5px;
            font-size: 7.5pt;
        }

        .badge-income {
            background: #B9F4C8;
            color: #166534;
        }

        .badge-expense {
            background: #F5D6D3;
            color: #B42318;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 14px 0 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #171717;
        }

        .empty {
            border: 1px dashed #999999;
            padding: 18px;
            text-align: center;
            color: #555555;
            font-size: 9pt;
            margin-top: 8px;
        }

        .note {
            font-size: 8pt;
            color: #555555;
            margin-top: 10px;
        }

        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                @if (! empty($logoPath))
                    <td class="logo-cell">
                        <img src="{{ $logoPath }}" alt="Logo INTRASOL" class="logo">
                    </td>
                @endif
                <td>
                    <div class="company">PT Integrasi Solusi Teknologi (INTRASOL)</div>
                    <div class="company-sub">Sistem Kas INTRASOL &middot; Laporan Keuangan</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">@yield('report-title')</div>
    <div class="meta">@yield('meta')</div>

    @yield('content')
</body>
</html>
