<!DOCTYPE html>
<html>

<head>
    <title>Surat Jalan</title>
    <style>
        body {
            font-family: 'Courier', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            font-weight: bold;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
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
            text-align: left;
        }

        .table td {
            padding: 5px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .signature-container {
            width: 100%;
            margin-top: 40px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 33%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-space {
            height: 70px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Asiwa Bumi Niaga</h2>
        <p>
            {{ $setting->alamat ?? 'Alamat Perusahaan' }}<br>
            Telp : {{ $setting->telepon ?? '-' }}
        </p>

        <h3>SURAT JALAN</h3>
    </div>

    <table style="width:100%;">
        <tr>
            <td>No. Surat Jalan</td>
            <td>: {{ $pengiriman->no_transaksi }}</td>

            <td class="text-right">Tanggal</td>
            <td>: {{ $pengiriman->created_at->format('d/m/Y') }}</td>
        </tr>

        <tr>
            <td>Customer</td>
            <td>: {{ $pengiriman->customer->nama }}</td>

            <td class="text-right">No. Polisi</td>
            <td>: {{ $pengiriman->nopol }}</td>
        </tr>

        <tr>
            <td>Supir</td>
            <td>: {{ $pengiriman->supir ?? '-' }}</td>

            <td class="text-right">Tujuan</td>
            <td>: {{ $pengiriman->tujuan ?? '-' }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Barang</th>
                <th width="20%" class="text-right">Qty</th>
                <th width="20%">Satuan</th>
            </tr>
        </thead>

        <tbody>

            @php $no = 1; @endphp

            @foreach($pengiriman->detail as $detail)

            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $detail->produk->nama_produk }}</td>
                <td class="text-right">
                    {{ number_format($detail->netto,2,',','.') }}
                </td>
                <td>{{ $detail->produk->satuan }}</td>
            </tr>

            @endforeach

        </tbody>
    </table>

    <div class="footer">

        <table style="width:100%;">
            <tr>
                <td>
                    <strong>Total Item :</strong>
                    {{ $pengiriman->detail->count() }}
                </td>

                <td class="text-right">
                    <strong>Total Berat :</strong>
                    {{ number_format($pengiriman->detail->sum('netto'),2,',','.') }}
                    Kg
                </td>
            </tr>
        </table>

        

    </div>

    <div class="signature-container">

        <table class="signature-table">

            <tr>

                <td>
                    Dibuat Oleh
                    <div class="signature-space"></div>
                    (...........................)
                </td>

                <td>
                    Pengemudi
                    <div class="signature-space"></div>
                    (...........................)
                </td>

                <td>
                    Penerima
                    <div class="signature-space"></div>
                    (...........................)
                </td>

            </tr>

        </table>

    </div>

</body>

</html>