<!DOCTYPE html>
<html>

<head>
    <title>Nota Pembelian</title>
    <style>
        body {
            font-family: 'Courier', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 5px;
        }

        .table td {
            padding: 5px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 20px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Asiwa Bumi Niaga</h2>
        <p>Alamat Lengkap Toko <br> Telp: -</p>
    </div>

    <table style="width: 100%">
        <tr>
            <td>No. Nota: {{ $pembelian->no_transaksi }}</td>
            <td class="text-right">Tgl: {{ $pembelian->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Supplier: {{ $pembelian->supplier->nama }}</td>
            <td></td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian->details as $detail)
            <tr>
                <td>{{ $detail->produk->nama_produk }}</td>
                <td>{{ $detail->netto }} {{ $detail->produk->satuan }}</td>
                <td>{{ number_format($detail->harga_basis, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($detail->harga_netto, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table style="width: 100%">
            <tr>
                <td style="width: 70%" class="text-right"><strong>Grand Total:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($pembelian->details->sum('harga_netto'), 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p></p>
    </div>
</body>

</html>