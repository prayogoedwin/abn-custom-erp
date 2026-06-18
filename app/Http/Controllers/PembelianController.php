<?php

namespace App\Http\Controllers;

use App\Exports\PembelianExport;
use App\Models\CashbonSupplierPembayaran;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\SimpanPinjamSupplier;
use App\Models\Supplier;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PembelianController extends Controller
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
        // 'no_transaksi',
        // 'supplier_id',
        // 'nopol',
        // 'tipe_transaksi_pembelian',
        // 'total_nominal_pembelian',
        // 'total_nominal_terbayar',
        // 'kekurangan',
        // 'status_pembayaran',

        $suppliers = Supplier::where('isActive', true)->get();
        // dd($suppliers);

        $pagedata = [
            'title' => 'Pembelian',
            'tablename' => 'pembelians',
            'tableaction' => true,
            'columns' => [
                ['name' => 'no_transaksi', 'value' => 'no_transaksi',  'title' => 'No Transaksi', 'type' => 'text', 'inform' => false, 'inshow' => true, 'intable' => true],
                ['name' => 'supplier_id', 'value' => 'supplier',  'title' => 'Supplier', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$suppliers->map(function ($supplier) {
                        return ['value' => $supplier->id, 'label' => $supplier->nama];
                    })->toArray(),
                ]],
                ['name' => 'nopol', 'value' => 'nopol',  'title' => 'Nopol', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],


                ['name' => 'total_nominal_pembelian', 'value' => 'total_nominal_pembelian',  'title' => 'Total Nominal Pembelian', 'type' => 'rupiah', 'inform' => true, 'inshow' => true, 'intable' => false],
                ['name' => 'total_nominal_terbayar', 'value' => 'total_nominal_pembelian',  'title' => 'Total Nominal Terbayar', 'type' => 'rupiah', 'inform' => true, 'inshow' => true, 'intable' => false],
                ['name' => 'kekurangan', 'value' => 'kekurangan',  'title' => 'Kekurangan', 'type' => 'rupiah', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'status_pembayaran', 'value' => 'status_pembayaran',  'title' => 'Status Pembayaran', 'type' => 'select', 'inform' => false, 'inshow' => true, 'intable' => true, 'options' => [
                    ['value' => 'Lunas', 'label' => 'Lunas'],
                    ['value' => 'Belum Lunas', 'label' => 'Belum Lunas'],
                ]],

            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        // $pembelians = Pembelian::join('suppliers', 'pembelians.supplier_id', '=', 'suppliers.id')
        //     // Select everything from pembelian, and specific fields from users
        //     ->select('pembelians.*', 'suppliers.nama as supplier')
        //     ->where('pembelians.isactive', true)
        //     ->get();
        // dd($pembelians);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $pembelians = Pembelian::join('suppliers', 'pembelians.supplier_id', '=', 'suppliers.id')
                // Select everything from pembelian, and specific fields from users
                ->select('pembelians.*', 'suppliers.nama as supplier')
                ->where('pembelians.isactive', true)
                ->get();
            // dd($pembelians);

            return DataTables::of($pembelians)
                ->editColumn('kekurangan', function ($pembelian) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($pembelian->kekurangan, 0, ',', '.');
                })



                ->addColumn('actions', function ($pembelian) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pembelians')) {
                        $actions .= '<a href="' . route('pembelians.show', $pembelian) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pembelians')) {
                        $actions .= '<a href="' . route('pembelians.edit', $pembelian) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pembelians')) {
                        $actions .= '<form action="' . route('pembelians.destroy', $pembelian) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.index', $pagedata);
    }

    public function export()
    {
        return Excel::download(new PembelianExport, 'pembelians-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $suppliers = Supplier::where('isActive', true)->get();

        $pagedata = $this->getPagedata();

        return view('pembelians.create', compact('suppliers'), $pagedata);
    }

    public function createlanjut(Pembelian $pembelian): View
    {
        // dd($pembelian);

        $supplier = Supplier::find($pembelian->supplier_id);



        $simpanpinjamsupplier = SimpanPinjamSupplier::where("supplier_id", $supplier->id)->first();

        $pagedata = $this->getPagedata();

        $data = $pembelian;
        $pembelian->load('details');
        // dd($pembelian);

        return view('pembelians.createlanjut', compact('pembelian', 'supplier', 'simpanpinjamsupplier', 'data'), $pagedata,);
    }

    public function store(Request $request): RedirectResponse
    {

        $store_data = [
            'nopol' => $request->input('nopol'),
            'supplier_id' => $request->input('supplier_id'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nopol' => ['required', 'string', 'max:255'],
            'supplier_id' => ['required', 'integer', 'max:255'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $pembelian = Pembelian::create($store_data);





        return to_route('pembeliandetails.createnow', $pembelian->id);
    }

    public function storelanjut(Pembelian $pembelian, Request $request): RedirectResponse
    {
        // dd($request->all());

        $store_data = [
            'potong_bon' => $this->toIntMoney($request->input('potong_bon')),
            'titip' => $this->toIntMoney($request->input('titip')),
            'ambil_tunai' => $this->toIntMoney($request->input('ambil_tunai')),
            'ambil_transfer' => $this->toIntMoney($request->input('ambil_transfer')),
            'status' => $request->input('status'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];



        $pembelian->update([
            'ambil_transfer' => $store_data['ambil_transfer'],
            'ambil_tunai' => $store_data['ambil_tunai'],
            'total_nominal_terbayar' => $store_data['ambil_transfer'] + $store_data['ambil_tunai'],
            'kekurangan' => $pembelian->total_nominal_pembelian - ($store_data['ambil_transfer'] + $store_data['ambil_tunai']),
            'status' => $store_data['status'],
        ]);




        CashbonSupplierPembayaran::create([

            'supplier_id' => $pembelian->supplier_id,
            'tipe' => 'Lewat Pembelian',
            'nominal_bayar' => $store_data['potong_bon'] ?? 0,
            'keterangan' => 'Lewat Pembelian' . $pembelian->no_transaksi,
            'created_by' => auth()->id(),

        ]);

        if ($request->input('action') === 'save_and_print') {
            return redirect()->route('pembelians.cetaknota', $pembelian)->with('success', 'Data berhasil disimpan!');
        }

        return to_route('pembelians.index')->with('success', 'Data berhasil disimpan!');;
    }



    public function cetakNota(Pembelian $pembelian)
    {
        // 1. Load relasi yang dibutuhkan
        $pembelian->load('details.produk', 'supplier');

        // 2. Cari riwayat pemotongan cashbon yang spesifik untuk pembelian ini
        // Menggunakan no_transaksi agar akurat dan tidak tertukar dengan transaksi lain
        $pembayarancashbon = CashbonSupplierPembayaran::where('supplier_id', $pembelian->supplier_id)
            ->where('keterangan', 'like', '%' . $pembelian->no_transaksi . '%')
            ->latest()
            ->first();

        $nominalPotong = $pembayarancashbon ? $pembayarancashbon->nominal_bayar : 0;

        $cashbonsebelum = $pembelian->supplier->totalCashbon() + $nominalPotong;

        $terbilang = $this->konversiTerbilang($pembelian->total_nominal_pembelian) . " Rupiah";
        // dd($pembelian, $pembayarancashbon, $cashbonsebelum);

        $pdf = Pdf::loadView('exports.nota', compact('pembelian', 'pembayarancashbon', 'cashbonsebelum', 'terbilang'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Nota-' . $pembelian->no_transaksi . '.pdf');
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
            $terbilang = " Seratus" . $this->konversiTerbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->konversiTerbilang($angka / 100) . " Ratus " . $this->konversiTerbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " Seribu" . $this->konversiTerbilang($angka - 1000);
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

    public function show($id): View
    {
        $pembelian = Pembelian::find($id);
        $pembelian->details = PembelianDetail::where('pembelian_id', $id)->get();
        $pembelian->supplier = Supplier::find($pembelian->supplier_id);

        $pagedata = $this->getPagedata();



        return view('pembelians.show', compact('pembelian'), $pagedata);
    }

    public function edit(Pembelian $pembelian): View
    {

        $data = $pembelian;


        $pagedata = $this->getPagedata();

        return view('pembelians.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Pembelian $pembelian): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nopol' => $request->input('nopol'),
            'supplier_id' => $request->input('supplier_id'),
            'tipe_transaksi_pembelian' => $request->input('tipe_transaksi_pembelian'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nopol' => ['required', 'string', 'max:255'],
            'supplier_id' => ['required', 'integer', 'max:255'],
            'tipe_transaksi_pembelian' => ['required', 'string'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $pembelian->update($store_data);



        if ($store_data['tipe_transaksi_pembelian'] === 'Titip') {
            return to_route('pembeliandetails.edittitip', $pembelian->id);
        }

        return to_route('pembeliandetails.editjual', $pembelian->id);
    }

    //soft delete
    public function destroy(Pembelian $pembelian): RedirectResponse
    {
        $pembelian->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pembelians.index')->with('status', 'Pembelian deleted successfully.');
    }
}
