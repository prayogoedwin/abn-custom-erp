<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembelian</title>
    <style>
        /* Mengimpor font bergaya mesin ketik / printer kasir */
        @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');

        body {
            font-family: 'Courier Prime', monospace;
            margin: 0;
            padding: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }


        

        .nota-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }

        .garis-putus {
            border-top: 1px dashed;
            margin: 12px 0;
        }

        .nota-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-bottom: 8px;
        }

        .nota-table th {
            text-transform: uppercase;
            font-size: 12px;
            font-weight: bold;
            padding-bottom: 4px;
        }

        .nota-table td {
            padding: 6px 0;
        }

        .text-right {
            text-align: right;
        }

        /* Mengatur lebar kolom agar proporsional */
        .col-barang {
            width: 15%;
        }

        .col-qtty {
            width: 15%;
        }

        .col-basis {
            width: 15%;
        }

        .col-rendemen {
            width: 15%;
        }

        .col-harga {
            width: 20%;
        }

        .col-jumlah {
            width: 20%;
        }

        .nota-rincian {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 8px;
        }

        .rincian-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .terbilang-section {
            font-size: 12px;
            text-transform: uppercase;
        }

        .terbilang-title {
            color: #4b5563;
            font-weight: bold;
        }

        .terbilang-content {
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Asiwa Bumi Niaga</h2>
        <p>Alamat Lengkap Toko <br> Telp: -</p>
    </div>

    <div class="nota-container">

        <div class="nota-header">
            <span>Nota Pembelian {{ $pembelian->no_transaksi}}</span>
            <span>Tanggal : {{ $pembelian->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="nota-header">
            <span>Supplier {{ $pembelian->supplier->nama}}</span>
            <span>Mobil : {{$pembelian->nopol}}</span>
        </div>

        <div class="garis-putus"></div>

        <table class="nota-table">
            <thead>
                <tr>
                    <th></th>
                    <th class="col-barang">Barang</th>
                    <th class="col-qtty text-right">Qtty</th>
                    <th class="col-basis text-right">Basis</th>
                    <th class="col-rendemen text-right">Rendemen</th>
                    <th class="col-harga text-right">Harga</th>
                    <th class="col-jumlah text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7">
                        <div class="garis-putus" style="margin: 0;"></div>
                    </td>
                </tr>

                @foreach($pembelian->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->produk->nama_produk }}</td>
                    <td class="text-right">{{ $detail->netto }} {{ $detail->produk->satuan }}</td>
                    <td class="text-right">{{ number_format($detail->harga_basis_pembelian, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->rendeman, 0, ',', '.') }} %</td>
                    <td class="text-right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->harga_netto, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="7">
                        <div class="garis-putus" style="margin: 0;"></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="nota-rincian">

            <div>
                <div class="rincian-row">
                    <span>Saldo Titipan</span>
                    <span></span>
                </div>
                <div class="rincian-row">
                    <span>Saldo Cashbon Rp.</span>
                    <span>{{ number_format($cashbonsebelum, 0, ',', '.') }}</span>
                </div>
            </div>

            <div>
                <div class="rincian-row">
                    <span>Total ............ Rp.</span>
                    <span>{{ number_format($pembelian->total_nominal_pembelian, 0, ',', '.') }}</span>
                </div>
                <div class="rincian-row">
                    <span>Pot Cashbon .... Rp.</span>
                    <span>{{ number_format($pembayarancashbon->nominal_bayar, 0, ',', '.') }}</span>
                </div>
                
                <div class="rincian-row">
                    <span>Sisa Cashbon ... Rp.</span>
                    <span>{{ number_format($pembelian->supplier->totalCashbon(), 0, ',', '.') }}</span>
                </div>
                <div class="rincian-row">
                    <span>Bank (transfer) Rp.</span>
                    <span>{{ number_format($pembelian->ambil_transfer, 0, ',', '.') }}</span>
                </div>
                <div class="rincian-row">
                    <span>Tunai ........... Rp.</span>
                    <span>{{ number_format($pembelian->ambil_tunai, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="garis-putus" style="margin: 16px 0;"></div>

        <div class="terbilang-section">
            <div class="terbilang-title">Terbilang :</div>
            <div class="terbilang-content">##EMPAT PULUH SEMBILAN JUTA TUJUH RATUS TUJUH PULUH RIBU RUPIAH##</div>
        </div>

    </div>

</body>

</html>