<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route($tablename . '.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Produks') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ $title ?? __('Edit Lanjut') }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Lanjut {{ $title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Prosses Edit Lanjut') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form action="{{ route($tablename . '.storelanjut', $data) }}" method="POST">
                @csrf
                @method('POST')

                <div class="mb-4">
                    <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Daftar Produk</h3>
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Produk</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3 text-center">Rendeman</th>
                                    <th class="px-4 py-3 text-center">Netto</th>
                                    <th class="px-4 py-3">Harga Basis</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($pembelian->details as $detail)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ $detail->produk->nama_produk }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ $detail->tipe_transaksi_pembelian }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $detail->rendeman ? $detail->rendeman . '%' : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ number_format($detail->netto, 2) }} <span class="text-xs text-gray-400">{{ $detail->satuan }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        Rp {{ number_format($detail->harga_basis_pembelian, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($detail->harga_netto, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-b border-gray-100 dark:border-gray-700 pb-6">
                    <div>
                        <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Informasi Transaksi</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kode Transaksi</label>
                                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $pembelian->no_transaksi }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Supplier</label>
                                <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $pembelian->supplier->nama ?? '-' }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</label>
                                <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $pembelian->created_at->format('d F Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    @php
                    $totalTagihan = $pembelian->details->sum('harga_netto');
                    $totalCashbonSupplier = $pembelian->supplier ? $pembelian->supplier->totalCashbon() : 0;
                    @endphp

                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700 space-y-4">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Ringkasan Pembayaran</h3>

                        <div class="space-y-2 text-sm border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Netto Keseluruhan:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pembelian->details->sum('netto'), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Tagihan Awal:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-red-500">
                                <span>Total Cashbon Supplier:</span>
                                <span class="font-semibold" id="label-total-cashbon" data-value="{{ $totalCashbonSupplier }}">Rp {{ number_format($totalCashbonSupplier, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        

                        <div class="flex justify-between text-xl border-t border-gray-200 dark:border-gray-700 pt-3 mt-2">
                            <span class="font-bold text-gray-700 dark:text-gray-300">Total Dibayarkan:</span>
                            <span class="font-black text-indigo-600 dark:text-indigo-400" id="total-tagihan-akhir" data-awal="{{ $totalTagihan }}">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-forms.input label="Ambil Tunai" name="ambil_tunai" class="ambil_tunai" type="number" value="{{ $pembelian->ambil_tunai ?? 0 }}" />
                    </div>
                    <div>
                        <x-forms.input label="Ambil Transfer" name="ambil_transfer" class="ambil_transfer" type="number" value="{{ $pembelian->ambil_transfer ?? 0 }}" />
                    </div>
                </div>
                

                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kekurangan: <span id="kekuranganspan" class="font-bold text-red-500">Rp 0</span> 
                        Sisa Bon: <span id="sisa-bon-live" class="font-bold text-gray-700 dark:text-gray-300">Rp 0</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-forms.input label="Titip Uang/Modal" disabled name="titip" class="titip" type="number" min="0" value="{{ $pembelian->titip ?? 0 }}" />
                    </div>
                    <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 pt-4">
                        Titip Uang Modal tidak dapat di edit karena sudah di proses, jika ingin mengubahnya lewat menu Titipan Supplier
                    </p>
                    </div>
                </div>

                <input type="hidden" name="status" id="status_hidden" value="Belum Lunas">
                <input type="hidden" name="potong_bon" value="0">

                <!-- <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Simpan Sebagai
                    </label>
                    <select
                        name="status"
                        id="status_select"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40">
                        <option value="Belum Lunas">Belum Lunas</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                </div> -->

                <div class="mb-5">
                    <x-forms.input label="Keterangan" name="keterangan" type="text" value="{{ $pembelian->keterangan ?? '' }}" />
                </div>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <button type="submit" name="action" value="save" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">
                        Simpan
                    </button>

                    <button type="submit" name="action" value="save_and_print" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600">
                        Simpan & Cetak Nota
                    </button>

                    <a href="{{ route($tablename . '.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputTitip = document.getElementById('titip');
            const inputAmbilTunai = document.querySelector('input[name="ambil_tunai"]');
            const inputAmbilTransfer = document.querySelector('input[name="ambil_transfer"]');
            const inputStatusHidden = document.getElementById('status_hidden');
            const inputPotongBon = document.querySelector('input[name="potong_bon"]');

            console.log(inputAmbilTunai);

            const labelSisaBonLive = document.getElementById('sisa-bon-live');
            const labelTotalTagihanAkhir = document.getElementById('total-tagihan-akhir');
            const labelKekurangan = document.getElementById('kekuranganspan');
            const selectStatus = document.getElementById('status_select');

            // Flag untuk mendeteksi jika user mengubah status secara manual
            let userInteractedWithStatus = false;

            const totalCashbonAwal = parseFloat(document.getElementById('label-total-cashbon').getAttribute('data-value')) || 0;
            const tagihanAwal = parseFloat(labelTotalTagihanAkhir.getAttribute('data-awal')) || 0;

            function formatRupiah(angka) {
                const isNegative = angka < 0;
                const absoluteValue = Math.abs(angka);
                const formatted = 'Rp ' + new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(absoluteValue);
                return isNegative ? '-' + formatted : formatted;
            }

            function hitungOtomatis() {
                console.log("hitung");
                let nilaiAmbilTunai = parseFloat(inputAmbilTunai.value) || 0;
                let nilaiAmbilTransfer = parseFloat(inputAmbilTransfer.value) || 0;

                

                

                const tagihanAkhir = tagihanAwal
                labelTotalTagihanAkhir.textContent = formatRupiah(tagihanAkhir);

                labelSisaBonLive.textContent = formatRupiah(totalCashbonAwal);
                // 3. Hitung Kekurangan Pembayaran
                // Kekurangan = Total yang harus dibayar - (Tunai + Transfer yang diambil)
                const totalDiambil = nilaiAmbilTunai + nilaiAmbilTransfer;
                const kekurangan = tagihanAkhir - totalDiambil;
                const kekuranganWithCashbon = (tagihanAkhir + totalCashbonAwal) - totalDiambil;

                labelKekurangan.textContent = formatRupiah(kekuranganWithCashbon);

                // Styling warna teks kekurangan berdasarkan statusnya
                labelKekurangan.className = "font-bold text-red-500";
                inputStatusHidden.value = "Belum Lunas";


                if (kekurangan <= 0) {
                    labelKekurangan.className = "font-bold text-yellow-500";
                    inputStatusHidden.value = "Lunas";
                }
                if (kekuranganWithCashbon <= 0) {
                    labelKekurangan.className = "font-bold text-green-500";
                }

                //jika uang yang diambil lebih besar dari total tagihan, maka sisa nya untuk potong bon
                if (totalDiambil > tagihanAkhir) {
                    const sisaUntukPotongBon = totalDiambil - tagihanAkhir;
                    // console.log("sisa untuk potong bon: " + totalDiambil + " - " + tagihanAkhir + " = " + sisaUntukPotongBon);
                    let sisaBon = totalCashbonAwal - sisaUntukPotongBon;
                    labelSisaBonLive.textContent = formatRupiah(sisaBon);
                    inputPotongBon.value = sisaUntukPotongBon;
                }
                
                


                // if (kekurangan <= 0) {
                //     selectStatus.value = "Lunas";
                // } else {
                //     selectStatus.value = "Belum Lunas";
                // }

            }

            // Pasang event listener ketik (input)
            inputTitip.addEventListener('input', hitungOtomatis);
            inputAmbilTunai.addEventListener('input', hitungOtomatis);
            inputAmbilTransfer.addEventListener('input', hitungOtomatis);

            // // Deteksi jika user merubah select status secara sengaja (manual override)
            // selectStatus.addEventListener('change', function() {
            //     userInteractedWithStatus = true;
            // });

            // Jalankan kalkulasi awal saat halaman dimuat
            hitungOtomatis();
        });
    </script>
</x-layouts.app>