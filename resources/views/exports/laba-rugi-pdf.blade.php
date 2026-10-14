<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>

    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 4px;
            font-size: 17px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f0f0f0;
            text-align: left;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .section td {
            background: #f5f5f5;
            font-weight: bold;
        }

        .total td {
            font-weight: bold;
            border-top: 1px solid #555;
        }

        .note {
            margin-top: 18px;
            padding: 10px;
            border: 1px solid #ccc;
            font-size: 9px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>LAPORAN LABA RUGI</h2>

    <div class="subtitle">
        Periode
        {{ $tanggalMulai->format('d/m/Y') }}
        s.d.
        {{ $tanggalAkhir->format('d/m/Y') }}
        <br>
        Basis data operasional
    </div>

    <table>
        <thead>
            <tr>
                <th>Uraian</th>
                <th class="number">Nominal (Rp)</th>
            </tr>
        </thead>

        <tbody>
            <tr class="section">
                <td>PENDAPATAN</td>
                <td></td>
            </tr>

            <tr>
                <td>Penjualan berdasarkan subtotal</td>
                <td class="number">
                    {{ number_format($penjualan, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>PPN tercatat</td>
                <td class="number">
                    {{ number_format($ppn, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>PPh tercatat</td>
                <td class="number">
                    {{ number_format($pph, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>Nominal akhir transaksi</td>
                <td class="number">
                    {{ number_format($nominalAkhir, 0, ',', '.') }}
                </td>
            </tr>

            <tr class="section">
                <td>PENGELUARAN TERCATAT</td>
                <td></td>
            </tr>

            @forelse ($pembelianPerBarang as $barang)
            <tr>
                <td>{{ $barang->nama_barang }}</td>
                <td class="number">
                    {{ number_format($barang->total, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2">
                    Tidak ada pembelian pada periode ini.
                </td>
            </tr>
            @endforelse
            <tr>
                <td colspan="2">
                    <b>Biaya Operasional:</b>
                </td>
            </tr>

            @forelse ($pengeluaranPerKategori as $item)
            <tr>
                <td style="padding-left: 25px;">
                    {{ $item->kategori }}
                </td>
                <td class="number">
                    {{ number_format($item->total, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="padding-left: 25px;">
                    Tidak ada pengeluaran pada periode ini.
                </td>
            </tr>
            @endforelse

            <tr class="total">
                <td>Total pengeluaran tercatat</td>
                <td class="number">
                    {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </td>
            </tr>

            <tr class="total">
                <td>
                    Selisih penjualan dan pengeluaran tercatat
                    (belum dikurangi HPP)
                </td>
                <td class="number">
                    {{ number_format(
                        $labaKotor,
                        0, ',', '.'
                    ) }}
                </td>
            </tr>

            <tr>
                <td>Harga pokok penjualan (HPP)</td>
                <td class="number">{{ number_format($hpp, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td>Laba sebelum pajak</td>
                <td class="number">{{ number_format($labaKotor - $hpp, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td>Laba bersih</td>
                <td class="number">{{ number_format($labaBersih, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>



    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>