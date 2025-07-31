<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Stock Transactions PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #888;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #e5e7eb;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subheader {
            font-size: 14px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="header">Laporan Transaksi Stok</div>
    <div class="subheader">
        Periode:
        @if($dateFrom && $dateTo)
        {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}
        @else
        Semua Data
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>User</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>#{{ $trx->id }}</td>
                <td>{{ $trx->product->name ?? '-' }}</td>
                <td>{{ ucfirst($trx->type) }}</td>
                <td>{{ number_format($trx->quantity) }}</td>
                <td>{{ $trx->user->name ?? '-' }}</td>
                <td>{{ ucfirst($trx->status) }}</td>
                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $trx->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>