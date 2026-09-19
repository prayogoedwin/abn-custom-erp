<!DOCTYPE html>
<html>
<head>
    <title>Surat Jalan</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.35;
            font-weight: bold;
            color: #000;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }

        .company {
            text-align: center;
            margin-bottom: 14px;
        }

        .line {
            border: 0;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 0 0 2px 0;
            vertical-align: top;
        }

        .items th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 4px 0;
        }

        .items th.right,
        .items td.right {
            text-align: right;
        }

        .items td {
            padding: 6px 0 4px 0;
            vertical-align: top;
        }

        .weights {
            width: 280px;
            margin-left: auto;
            margin-top: 8px;
        }

        .weights td {
            padding: 1px 0;
        }

        .weights .rule td {
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        .footer-date {
            text-align: right;
            margin-top: 12px;
        }

        .ttd {
            margin-top: 28px;
        }

        .ttd td {
            width: 50%;
            vertical-align: top;
        }

        .ttd .right {
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $namaPerusahaan = (isset($setting) && ! empty($setting->nama)) ? $setting->nama : 'CV ASIWA BUMI NIAGA';
        $alamatPerusahaan = (isset($setting) && ! empty($setting->alamat)) ? $setting->alamat : "Jl. Kol. Wahab Uzir No.930\nBaturaja Timur";
        $kota = (isset($setting) && ! empty($setting->kota)) ? $setting->kota : 'Baturaja';
        $customer = $pengiriman->customer;
        $totalBruto = (float) $pengiriman->detail->sum('bruto');
        $totalTara = (float) $pengiriman->detail->sum('tara');
        $totalNetto = (float) $pengiriman->detail->sum('netto');
        $fmtKg = fn ($nilai) => number_format((float) $nilai, 2, ',', '.');
        $fmtKarung = function ($nilai) {
            $angka = (float) $nilai;
            $desimal = fmod($angka, 1.0) == 0.0 ? 0 : 2;

            return number_format($angka, $desimal, ',', '.');
        };
    @endphp

    <div class="company">
        {{ $namaPerusahaan }}<br>
        {!! nl2br(e($alamatPerusahaan)) !!}
    </div>

    <div>
        Kepada Yth.<br>
        {{ $customer->nama ?? '-' }}<br>
        @if($customer && $customer->alamat)
            {!! nl2br(e($customer->alamat)) !!}
        @endif
    </div>

    <table class="meta" style="margin-top: 10px;">
        <tr>
            <td>Surat Jalan No. {{ $pengiriman->no_transaksi }}</td>
            <td class="right">Mobil {{ $pengiriman->nopol }}</td>
        </tr>
    </table>
    <hr class="line">

    <table class="items">
        <thead>
            <tr>
                <th style="width: 6%;"></th>
                <th style="width: 42%;">NAMA BARANG</th>
                <th style="width: 22%;">BANYAKNYA</th>
                <th class="right" style="width: 30%;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengiriman->detail as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ strtoupper($detail->nama_barang ?: ($detail->produk->nama_produk ?? '-')) }}</td>
                <td>{{ $fmtKarung($detail->jumlah_karung) }} KARUNG</td>
                <td class="right nowrap">{{ $fmtKg($detail->bruto) }} KG</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="weights">
        <tr>
            <td>BRUTO..</td>
            <td class="right">{{ $fmtKg($totalBruto) }} KG</td>
        </tr>
        <tr>
            <td>TARA...(</td>
            <td class="right">{{ $fmtKg($totalTara) }}) KG</td>
        </tr>
        <tr class="rule">
            <td>NETTO..</td>
            <td class="right">{{ $fmtKg($totalNetto) }} KG</td>
        </tr>
    </table>

    <div class="footer-date">
        {{ $kota }}, {{ $pengiriman->created_at->format('d/m/Y') }}
    </div>

    <table class="ttd">
        <tr>
            <td>TANDA TERIMA,</td>
            <td class="right">
                PENGIRIM,<br>
                {{ $namaPerusahaan }}
            </td>
        </tr>
    </table>
</body>
</html>
