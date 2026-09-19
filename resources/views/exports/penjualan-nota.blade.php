<!DOCTYPE html>
<html>
<head>
    <title>Invoice Penjualan</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.35;
            font-weight: bold;
            color: #000;
            margin: 18px;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }

        .company {
            text-align: center;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .company-name {
            font-size: 16px;
            letter-spacing: 1px;
        }

        .title {
            text-align: center;
            letter-spacing: 8px;
            font-size: 16px;
            margin: 12px 0 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            vertical-align: top;
            padding: 0;
        }

        .items th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 2px;
            text-align: left;
        }

        .items td {
            padding: 6px 2px;
            vertical-align: top;
        }

        .items th.right,
        .items td.right {
            text-align: right;
        }

        .totals {
            width: 420px;
            margin-left: auto;
            margin-top: 14px;
        }

        .totals td {
            padding: 2px 0;
        }

        .bank {
            width: 420px;
            margin-left: auto;
            margin-top: 12px;
        }

        .bank td {
            padding: 1px 0;
            vertical-align: top;
        }

        .ttd {
            width: 280px;
            margin-left: auto;
            margin-top: 18px;
            text-align: center;
        }

        .ttd-space {
            height: 56px;
        }
    </style>
</head>
<body>
    @php
        $namaPerusahaan = (isset($setting) && ! empty($setting->nama)) ? $setting->nama : 'CV. ASIWA BUMI NIAGA';
        $alamatPerusahaan = (isset($setting) && ! empty($setting->alamat))
            ? $setting->alamat
            : "Jalan Kolonel Wahab Uzir NO.930, Kelurahan Sukajadi\nKecamatan Baturaja Timur, Sumatera Selatan";
        $npwp = (isset($setting) && ! empty($setting->npwp)) ? $setting->npwp : '53.695.315.1-302.000';
        $kota = (isset($setting) && ! empty($setting->kota)) ? $setting->kota : 'Baturaja';
        $bank = (isset($setting) && ! empty($setting->bank)) ? $setting->bank : 'BANK CENTRAL ASIA';
        $rekening = (isset($setting) && ! empty($setting->rekening)) ? $setting->rekening : '2570799269';
        $atasNama = (isset($setting) && ! empty($setting->atas_nama)) ? $setting->atas_nama : 'ASIWA BUMI NIAGA CV';
        $penandatangan = (isset($setting) && ! empty($setting->direktur)) ? $setting->direktur : 'IWAN SAPUTRA';
        $customer = $penjualan->customer ?? $penjualan->pengiriman?->customer;
        $detailsJual = $penjualan->detailsJual();
        $jumlah = $penjualan->invoiceJumlah();
        $ppn = $penjualan->invoicePpn();
        $pph = $penjualan->invoicePph22();
        $totalDibayar = $penjualan->invoiceTotalDibayar();
        $uang = fn ($nilai) => 'Rp. '.number_format((int) $nilai, 0, ',', '.');
        $fmtKg = fn ($nilai) => number_format((float) $nilai, 2, ',', '.');
        $fmtHarga = fn ($nilai) => 'Rp. '.number_format((float) $nilai, 3, ',', '.');
        $noSuratJalan = $penjualan->pengiriman?->no_transaksi ?? '-';
    @endphp

    <div class="company">
        <div class="company-name">{{ $namaPerusahaan }}</div>
        {!! nl2br(e($alamatPerusahaan)) !!}<br>
        NPwP : {{ $npwp }}
    </div>

    <div class="title">I N V O I C E</div>

    <table class="meta">
        <tr>
            <td style="width: 70%;">
                Kepada Yth.<br>
                {{ $customer->nama ?? '-' }}<br>
                @if($customer && $customer->alamat)
                    {!! nl2br(e($customer->alamat)) !!}
                @endif
            </td>
            <td class="right nowrap">
                Nomor : {{ $penjualan->no_transaksi_penjualan }}
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top: 14px;">
        <thead>
            <tr>
                <th style="width: 22%;">No. Srt Jalan</th>
                <th style="width: 18%;">BARANG</th>
                <th class="right" style="width: 20%;">QTTY</th>
                <th class="right" style="width: 20%;">HARGA</th>
                <th class="right" style="width: 20%;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailsJual as $index => $detail)
            <tr>
                <td>{{ $index + 1 }} {{ $noSuratJalan }}</td>
                <td>{{ strtoupper($detail->produk->nama_produk ?? '-') }}</td>
                <td class="right nowrap">{{ $fmtKg($detail->netto) }} {{ strtoupper($detail->produk->satuan ?? 'KG') }}</td>
                <td class="right nowrap">{{ $fmtHarga($detail->hargaUntukNota()) }}</td>
                <td class="right nowrap">{{ $uang($detail->sub_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>JUMLAH</td>
            <td class="right">{{ $uang($jumlah) }}</td>
        </tr>
        <tr>
            <td>PPN &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.1%</td>
            <td class="right">{{ $uang($ppn) }}</td>
        </tr>
        <tr>
            <td>PPH 22 &nbsp;&nbsp;&nbsp;&nbsp;0,25%</td>
            <td class="right">{{ $uang($pph) }}</td>
        </tr>
        <tr>
            <td>TOTAL YANG DIBAYARKAN</td>
            <td class="right">{{ $uang($totalDibayar) }}</td>
        </tr>
    </table>

    <table class="bank">
        <tr>
            <td style="width: 38%;">TRANSFER KE :</td>
            <td></td>
        </tr>
        <tr>
            <td>BANK</td>
            <td>: {{ $bank }}</td>
        </tr>
        <tr>
            <td>AC</td>
            <td>: {{ $rekening }}</td>
        </tr>
        <tr>
            <td>AN.</td>
            <td>: {{ $atasNama }}</td>
        </tr>
    </table>

    <div class="ttd">
        {{ $kota }}, {{ $penjualan->created_at->format('d/m/Y') }}<br>
        CV ASIWA BUMI NIAGA
        <div class="ttd-space"></div>
        {{ $penandatangan }}<br>
        Direktur
    </div>
</body>
</html>
