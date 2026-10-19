<?php

namespace App\Http\Controllers;

use App\Exports\PenjualanExport;
use App\Models\Customer;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\SimpanPinjamSupplier;
use App\Models\Supplier;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    private function toIntMoney($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        $cleaned = preg_replace('/[^\d\-]/', '', (string) $value);
        return $cleaned === '' || $cleaned === '-' ? 0 : (int) $cleaned;
    }

    private function getPagedata()
    {

        // $fillable = [
        //     'no_transaksi_penjualan',
        //     'pengiriman_id',
        //     'customer_id',

        //     'deleted_at',
        //     'created_by',
        //     'updated_by',
        //     'deleted_by',
        // ];

        $pengirimans = Pengiriman::with('customer')->where('deleted_at', null)->get();
        $customers = Customer::where('deleted_at', null)->get();
        // dd($suppliers);

        $pagedata = [
            'title' => 'Penjualan',
            'tablename' => 'penjualans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'no_transaksi_penjualan', 'value' => 'no_transaksi_penjualan',  'title' => 'No Transaksi', 'type' => 'text', 'inform' => false, 'inshow' => true, 'intable' => true],
                ['name' => 'pengiriman_id', 'value' => 'pengiriman',  'title' => 'Pengiriman', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$pengirimans->map(function ($pengiriman) {
                        $label = $pengiriman->no_transaksi . ' - ' . $pengiriman->nopol . ' - ' . ($pengiriman->customer?->nama ?? '-');
                        return ['value' => $pengiriman->id, 'label' => $label];
                    })->toArray(),
                ]],

                ['name' => 'customer_id', 'value' => 'customer',  'title' => 'Customer', 'type' => 'select', 'inform' => false, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$customers->map(function ($customer) {
                        return ['value' => $customer->id, 'label' => $customer->nama];
                    })->toArray(),
                ]],


            ],
        ];

        return $pagedata;
    }

    public function index()
    {


        $pagedata = $this->getPagedata();

        return view('penjualans.index', $pagedata);
    }

    public function indexTable(Request $request)
    {
        if ($request->ajax()) {
            $penjualans = Penjualan::with('customer', 'details.produk')
                ->select('penjualans.*');

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $penjualans->whereBetween('penjualans.created_at', [$start, $end]);
            }

            return DataTables::of($penjualans)
                ->addColumn('no_transaksi', function ($penjualan) {
                    return $penjualan->no_transaksi_penjualan;
                })
                ->filterColumn('no_transaksi', function ($query, $keyword) {
                    $query->where('no_transaksi_penjualan', 'like', "%{$keyword}%");
                })
                ->addColumn('customer', function ($penjualan) {
                    return $penjualan->customer ? $penjualan->customer->nama : '';
                })
                ->filterColumn('customer', function ($query, $keyword) {
                    $query->whereHas('customer', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('detail', function ($penjualan) {
                    $produkNames = $penjualan->details->map(function ($detail) {
                        return   '[' . $detail->tipe . '] ' . ($detail->produk?->nama_produk ?? '-') . ' (' . $detail->netto . ' ' . ($detail->produk?->satuan ?? '-') . ')';
                    })->toArray();
                    $content = implode('<br>', $produkNames);
                    return '<div style="max-height: 100px; overflow-y: auto; white-space: nowrap;">' . $content . '</div>';
                })
                ->filterColumn('detail', function ($query, $keyword) {
                    $query->whereHas('details.produk', function ($q) use ($keyword) {
                        $q->where('nama_produk', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->translatedFormat('d M Y');
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })



                ->addColumn('actions', function ($penjualan) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-penjualans')) {
                        $actions .= '<a href="' . route('penjualans.show', $penjualan) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-penjualans')) {
                        $actions .= '<a href="' . route('penjualans.edit', $penjualan) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-penjualans')) {
                        $actions .= '<form action="' . route('penjualans.destroy', $penjualan) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions', 'detail']) // Menandai kolom actions dan detail sebagai raw HTML
                ->make(true);
        }

        return response()->json(['message' => 'Invalid request'], 400);
    }

    public function export()
    {
        //TODO:
        // return Excel::download(new PenjualanExport, 'penjualans-' . date('Y-m-d') . '.xlsx');
    }

    public function exportXmlCoretax(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        $query = Penjualan::query()
            ->with([
                'customer',
                'details.produk',
            ])
            ->orderBy('created_at')
            ->orderBy('id');

        if (!empty($validated['tanggal_mulai'])) {
            $query->whereDate(
                'created_at',
                '>=',
                $validated['tanggal_mulai']
            );
        }

        if (!empty($validated['tanggal_selesai'])) {
            $query->whereDate(
                'created_at',
                '<=',
                $validated['tanggal_selesai']
            );
        }

        $penjualans = $query->get();

        $tin = Setting::where('key', 'TIN')->first()?->value;
        $idTku = Setting::where('key', 'IdTKU')->first()?->value;

        if (!$tin || !$idTku) {
            return response()->json([
                'message' => 'TIN atau IdTKU belum dikonfigurasi.',
            ], 422);
        }

        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->xmlStandalone = true;
        $xml->formatOutput = true;

        // Root
        $root = $xml->createElement('SimpleBookKeepingBulk');
        $root->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );

        $xml->appendChild($root);

        // TIN
        $root->appendChild(
            $xml->createElement('TIN', $tin)
        );

        // Daftar transaksi
        $list = $xml->createElement('ListOfTransaction');
        $root->appendChild($list);

        // Helper untuk membuat elemen dengan text aman
        $addElement = function ($parent, $name, $value) use ($xml) {
            $element = $xml->createElement($name);

            if ($value !== null && $value !== '') {
                $element->appendChild(
                    $xml->createTextNode((string) $value)
                );
            }

            $parent->appendChild($element);

            return $element;
        };

        // Format angka tanpa pemisah ribuan
        $formatNumber = function ($value) {
            $number = number_format(
                (float) ($value ?? 0),
                4,
                '.',
                ''
            );

            return rtrim(rtrim($number, '0'), '.');
        };

        foreach ($penjualans as $penjualan) {
            $transaction = $xml->createElement('Transaction');
            $list->appendChild($transaction);

            // Identitas transaksi
            $addElement($transaction, 'IdTku', $idTku);

            $addElement(
                $transaction,
                'TransactionNumber',
                $penjualan->no_transaksi_penjualan
            );

            $tanggal = $penjualan->created_at
                ? Carbon::parse($penjualan->created_at)
                ->format('Y-m-d')
                : '';

            $addElement(
                $transaction,
                'TransactionDate',
                $tanggal
            );

            // Sesuaikan 'nama' dengan kolom nama customer Anda
            $customer = $penjualan->customer?->nama ?? '';

            $addElement(
                $transaction,
                'Customer',
                $customer
            );

            // Detail barang/jasa
            $detailsTransaction = $xml->createElement(
                'DetailsTransaction'
            );

            $transaction->appendChild($detailsTransaction);

            foreach ($penjualan->details as $detail) {
                $detailElement = $xml->createElement('Detail');

                $detailsTransaction->appendChild($detailElement);

                // Sesuaikan nama kolom produk jika berbeda
                $namaProduk = $detail->produk?->nama_produk
                    ?? $detail->produk?->nama
                    ?? '';

                $addElement(
                    $detailElement,
                    'DetailsOfGoodService',
                    $namaProduk
                );

                // Asumsi basis_harga adalah harga per unit
                $addElement(
                    $detailElement,
                    'PricePerUnit',
                    $formatNumber($detail->basis_harga)
                );

                // Asumsi netto adalah kuantitas barang
                $addElement(
                    $detailElement,
                    'Qty',
                    $formatNumber($detail->netto)
                );
            }

            // Model belum memiliki kolom diskon.
            // Ubah jika diskon transaksi tersedia.
            $addElement($transaction, 'Discount', '0');
        }

        $filename = 'penjualan-coretax-'
            . now()->format('Ymd-His')
            . '.xml';

        return response($xml->saveXML(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'
                . $filename
                . '"',
        ]);
    }

    public function create(): View
    {

        $customers = Customer::where('deleted_at', null)->get();
        $pengirimandetails = PengirimanDetail::where('deleted_at', null)->get();
        $produks = Produk::where('deleted_at', null)->get();

        //cuma ambil pengiriman yang belum ada penjualan
        //pastikan juga penjualans deleted_at null, karena kalau penjualan dihapus, pengiriman bisa dipakai lagi
        $pengirimans = Pengiriman::with('customer')->where('deleted_at', null)->whereNotIn('id', function ($query) {
            $query->select('pengiriman_id')->from('penjualans')->where('deleted_at', null);
        })->get();




        $pagedata = $this->getPagedata();

        return view('penjualans.create', compact('customers', 'pengirimans', 'pengirimandetails', 'produks'), $pagedata);
    }


    public function store(Request $request): RedirectResponse
    {

        // dd($request->all());
        // dd(PengirimanDetail::find(1));


        $store_data = [
            'pengiriman_id' => $request->input('pengiriman_id'),

            'created_by' => auth()->id(),
        ];

        $pengiriman = Pengiriman::find($store_data['pengiriman_id']);
        $store_data['no_transaksi_penjualan'] = $pengiriman?->no_transaksi;
        $store_data['customer_id'] = $pengiriman?->customer?->id;


        $validate = Validator::make($store_data, [
            'pengiriman_id' => ['required', 'integer', 'max:255'],

            'created_by' => ['required', 'integer']
        ]);

        if ($request->pengiriman_detail_id != null) {
            foreach ($request->pengiriman_detail_id as $index => $pengiriman_detail_id) {
                $validate = Validator::make($request->all(), [
                    'pengiriman_detail_id.' . $index => ['required', 'integer'],
                    'produk_id.' . $index => ['required', 'integer'],
                    'tipe.' . $index => ['required', 'string', 'max:255'],
                    'netto.' . $index => ['required', 'numeric'],
                    'rendeman.' . $index => ['nullable', 'numeric'],
                    'bobot.' . $index => ['nullable', 'integer'],
                    'selisih.' . $index => ['required', 'numeric'],
                    'basis_harga.' . $index => ['required', 'numeric'],
                    'sub_total.' . $index => ['required', 'numeric'],
                    'pph.' . $index => ['nullable', 'numeric'],
                    'ppn.' . $index => ['nullable', 'numeric'],
                    'nominal_akhir.' . $index => ['required', 'numeric'],
                ]);
            }
        }


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $penjualan = Penjualan::create($store_data);
        if ($request->pengiriman_detail_id != null) {
            foreach ($request->pengiriman_detail_id as $index => $pengiriman_detail_id) {
                if ($pengiriman_detail_id) {

                    $pengirimandetail = PengirimanDetail::find($pengiriman_detail_id);

                    $detail = [
                        'selisih' => $request->selisih[$index],
                        'sub_total' => $request->sub_total[$index],
                        'pph' => $request->pph[$index],
                        'ppn' => $request->ppn[$index],
                        'nominal_akhir' => $request->nominal_akhir[$index],
                        'bobot' => $request->bobot[$index],
                        'rendeman' => $request->rendeman[$index],

                        'pengiriman_detail_id' => $pengiriman_detail_id,
                        'produk_id' => $request->produk_id[$index],
                        'tipe'    => $request->tipe[$index],
                        'netto_pengiriman'    => $pengirimandetail->netto,
                        'netto'    => $request->netto[$index],
                    ];

                    if ($request->tipe[$index] == "Titip") {
                        $detail['sub_total'] = 0;
                        $detail['pph'] = 0;
                        $detail['ppn'] = 0;
                        $detail['nominal_akhir'] = 0;
                    }



                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'pengiriman_detail_id' => $request->pengiriman_detail_id[$index],
                        'produk_id' => $request->produk_id[$index],
                        'tipe'    => $request->tipe[$index],
                        'netto_pengiriman'    => $pengirimandetail->netto,
                        'netto'    => $request->netto[$index],
                        'bobot'    => $request->bobot[$index],
                        'rendeman'    => $request->rendeman[$index],
                        'selisih'    => $detail['selisih'],
                        'basis_harga'    => $request->basis_harga[$index],
                        'sub_total'    => $detail['sub_total'],
                        'pph'    => $detail['pph'] ? $detail['pph'] : 0,
                        'ppn'    => $detail['ppn'],
                        'nominal_akhir'    => $detail['nominal_akhir'],

                        'created_by' => $store_data['created_by'],
                    ]);
                }
            }
        }





        return to_route('penjualans.index')->with('status', "penjualans created succesfully");
    }


    public function show(int $id): View
    {
        $penjualan = Penjualan::find($id);
        // dd($penjualan);

        // dd($penjualan->details);
        // dd(PenjualanDetail::get());


        $pagedata = $this->getPagedata();



        return view('penjualans.show', compact('penjualan'), $pagedata);
    }

    public function cetakNota(Penjualan $penjualan)
    {
        $penjualan->load('details.produk', 'pengiriman.customer', 'customer');
        $terbilang = $this->konversiTerbilang($penjualan->invoiceTotalDibayar());

        $pdf = Pdf::loadView('exports.penjualan-nota', compact('penjualan', 'terbilang'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-Penjualan-' . $penjualan->no_transaksi_penjualan . '.pdf');
    }

    // Tampilkan halaman edit (data sama persis dengan cetak biasa)
    public function editNota(Penjualan $penjualan)
    {
        $penjualan->load('details.produk', 'pengiriman.customer', 'customer');
        $terbilang = $this->konversiTerbilang($penjualan->invoiceTotalDibayar());
        // seperti yang sudah Anda lakukan di method cetak sekarang
        return view('exports.penjualan-nota-edit', compact(
            'penjualan',
            'terbilang',

        ));
    }

    // Opsi B: HTML hasil edit -> PDF (tidak menyentuh database)
    public function cetakNotaEdit(Request $request)
    {
        $request->validate(['html' => 'required|string']);

        return Pdf::loadHTML($request->input('html'))
            ->setPaper('a4', 'portrait')
            ->download('Nota-Edit-' . now()->format('YmdHis') . '.pdf');
    }

    private function konversiTerbilang(int $angka)
    {
        $angka = abs((int)$angka);
        $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->konversiTerbilang($angka - 10) . " Belas ";
        } elseif ($angka < 100) {
            $terbilang = $this->konversiTerbilang($angka / 10) . " Puluh " . $this->konversiTerbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = " Seratus " . $this->konversiTerbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->konversiTerbilang($angka / 100) . " Ratus " . $this->konversiTerbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " Seribu " . $this->konversiTerbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->konversiTerbilang($angka / 1000) . " Ribu " . $this->konversiTerbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->konversiTerbilang($angka / 1000000) . " Juta " . $this->konversiTerbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = $this->konversiTerbilang($angka / 1000000000) . " Milyar " . $this->konversiTerbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $terbilang = $this->konversiTerbilang($angka / 1000000000000) . " Trilyun " . $this->konversiTerbilang(fmod($angka, 1000000000000));
        }

        return trim($terbilang);
    }

    public function edit(Penjualan $penjualan): View
    {

        $data = $penjualan;

        $customers = Customer::where('deleted_at', null)->get();

        $produks = Produk::get();

        $data->load('pengiriman.detail.produk', 'pengiriman.customer');
        // dd($pengirimans->toArray());

        $pagedata = $this->getPagedata();

        return view('penjualans.edit', compact('data', 'customers', 'produks'), $pagedata);
    }

    public function update(Request $request, Penjualan $penjualan): RedirectResponse
    {
        $store_data = [

            'created_by' => auth()->id(),
        ];

        $pengiriman = Pengiriman::find($penjualan->pengiriman_id);
        $store_data['no_transaksi_penjualan'] = $pengiriman->no_transaksi;
        $store_data['customer_id'] = $pengiriman->customer->id;


        $validate = Validator::make($store_data, [
            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $penjualan->update($store_data);
        // Hapus semua dulu
        PenjualanDetail::where('penjualan_id', $penjualan->id)->delete();

        if ($request->pengiriman_detail_id != null) {
            foreach ($request->pengiriman_detail_id as $index => $pengiriman_detail_id) {
                if ($pengiriman_detail_id) {

                    $pengirimandetail = PengirimanDetail::find($pengiriman_detail_id);

                    $detail = [
                        'selisih' => $request->selisih[$index],
                        'sub_total' => $request->sub_total[$index],
                        'pph' => $request->pph[$index],
                        'ppn' => $request->ppn[$index],
                        'nominal_akhir' => $request->nominal_akhir[$index],
                    ];

                    if ($request->tipe[$index] == "Titip") {
                        $detail['sub_total'] = 0;
                        $detail['pph'] = 0;
                        $detail['ppn'] = 0;
                        $detail['nominal_akhir'] = 0;
                    }

                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'pengiriman_detail_id' => $request->pengiriman_detail_id[$index],
                        'produk_id' => $request->produk_id[$index],
                        'tipe'    => $request->tipe[$index],
                        'netto_pengiriman'    => $pengirimandetail->netto,
                        'netto'    => $request->netto[$index],
                        'selisih'    => $detail['selisih'],
                        'basis_harga'    => $request->basis_harga[$index],
                        'sub_total'    => $detail['sub_total'],
                        'pph'    => $detail['pph'] ? $detail['pph'] : 0,
                        'ppn'    => $detail['ppn'] ? $detail['ppn'] : 0,
                        'nominal_akhir'    => $detail['nominal_akhir'],

                        'created_by' => $store_data['created_by'],
                    ]);
                }
            }
        }




        return to_route('penjualans.index')->with('status', 'Penjualan updated successfully.');
    }

    //soft delete
    public function destroy(Penjualan $penjualan): RedirectResponse
    {

        $penjualan->update(['deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('penjualans.index')->with('status', 'Penjualan deleted successfully.');
    }
}
