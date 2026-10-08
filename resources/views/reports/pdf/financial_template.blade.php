<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Financial Audit Report</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #111; padding-bottom: 15px; }
        .header h1 { margin: 0 0 5px 0; font-size: 20px; text-transform: uppercase; }
        .header h2 { margin: 0 0 10px 0; font-size: 14px; color: #555; font-weight: normal; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        /* Evitar que las filas se corten por la mitad en el salto de página del PDF */
        tr { page-break-inside: avoid; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        
        .amount { text-align: right; font-family: "Courier New", Courier, monospace; font-weight: bold; }
        .status { text-transform: capitalize; }
        
        /* Footer fijo para todas las páginas */
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 40px; border-top: 1px solid #ddd; padding-top: 10px; }
        .footer table { width: 100%; border: none; margin: 0; }
        .footer td { border: none; padding: 0; font-size: 9px; color: #777; }
        
        /* Numeración de páginas nativa de DomPDF */
        .page-number:before { content: "Page " counter(page) " of " counter(pages); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Audit Report</h1>
        <!-- Inyección del Tenant para evitar fugas de contexto en documentos impresos -->
        <h2>{{ $company_name ?? 'Internal Audit' }}</h2>
        <p>Generated strictly for internal analysis. Confidential.</p>
        <p>Generated At: {{ now()->setTimezone($timezone ?? 'UTC')->format('F j, Y, g:i A') }} ({{ $timezone ?? 'UTC' }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Status</th>
                <th class="amount">Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Removido array_slice para permitir el renderizado masivo real -->
            @forelse($data as $row)
            <tr>
                <td>{{ $row['transaction_id'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td class="status">{{ $row['status'] }}</td>
                <td class="amount">{{ number_format((float)$row['amount'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px;">No transactions found for the specified period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td style="text-align: left;">System Generated Document - Do not distribute.</td>
                <td style="text-align: right;" class="page-number"></td>
            </tr>
        </table>
    </div>
</body>
</html>