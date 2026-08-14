<!DOCTYPE html>
<html>

<head>
    <title>Titipan Gudang ABN</title>
    <style>
        body {
            font-family: 'Courier', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            font-weight: bold;
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
            border-bottom: 1px solid #000;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .footer {
            margin-top: 15px;
        }

        .terbilang-box {
            border: 1px solid #000;
            padding: 8px;
            margin-top: 10px;
            margin-bottom: 25px;
            font-style: italic;
        }

        /* Style Tambahan untuk Tanda Tangan */
        .signature-container {
            width: 100%;
            margin-top: 30px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-space {
            height: 70px;
            /* Jarak untuk tanda tangan manual */
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Titipan Gudang ABN</h2>
        <p>Alamat Lengkap Toko <br> Telp: -</p>
    </div>

    <table style="width: 100%">
        <tr>
            <td>No. Nota: {{ $pembelian->no_transaksi }}</td>
            <td class="text-right">Tgl: {{ $pembelian->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Dari: {{ $pembelian->supplier->nama }}</td>
            <td class="text-right">Mobil: {{ $pembelian->nopol }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="text-align: left;">BARANG</th>
                <th class="text-right">QTTY</th>
                <th class="text-right">RENDEMEN</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian->details as $detail)
            <!-- hanya yang tipe jual -->
            @if($detail->tipe_transaksi_pembelian == 'titip')
            <tr>
                <td>{{ $detail->produk->nama_produk }}</td>
                <td class="text-right">{{ $detail->netto }} {{ $detail->produk->satuan }}</td>
                <td class="text-right">{{ number_format($detail->rendeman, 0, ',', '.') }} %</td>
                
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table style="width: 100%">
            <!-- BARIS DAFTAR PRODUK TITIPAN (BISA BANYAK) -->
            

            <!-- BARIS KEDUA: SALDO & GRAND TOTAL -->
            <tr>
                
                <td class="text-right col-6"><strong>Total: </strong></td>
                <td class="text-right"><strong>{{ number_format($pembelian->details->where('tipe_transaksi_pembelian', 'titip')->sum('netto'), 0, ',', '.') }} kg</strong></td>
            </tr>

            
        </table>
    </div>

    

    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td>

                </td>
                <td>
                    <p>{{ $setting->alamat ?? 'Baturaja' }} | {{ date('d/m/Y') }}</p>
                    <div class="signature-space"></div>
                    <p>( ........................ )</p>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>