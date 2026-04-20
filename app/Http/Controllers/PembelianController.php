<?php

namespace App\Http\Controllers;

use App\Exports\PembelianExport;
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
                ['name' => 'nominal', 'value' => 'nominal',  'title' => 'Nominal', 'type' => 'number', 'inform' => true, 'inshow' => false, 'intable' => false],
                ['name' => 'tipe_transaksi_pembelian', 'value' => 'tipe_transaksi_pembelian',  'title' => 'Tipe Transaksi Pembelian', 'type' => 'select', 'inform' => true, 'inshow' => true, 'intable' => true, 'options' => [
                    ['value' => 'Titip', 'label' => 'Titip'],
                    ['value' => 'Jual', 'label' => 'Jual'],
                ]],
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
        //     // Select everything from karyawan, and specific fields from users
        //     ->select('pembelians.*', 'suppliers.nama as supplier')
        //     ->where('pembelians.isactive', true)
        //     ->get();
        // dd($pembelians);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $pembelians = Pembelian::join('suppliers', 'pembelians.supplier_id', '=', 'suppliers.id')
                // Select everything from karyawan, and specific fields from users
                ->select('pembelians.*', 'suppliers.nama as supplier')
                ->where('pembelians.isactive', true)
                ->get();
            // dd($pembelians);

            return DataTables::of($pembelians)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('pembelians.nama_karyawan', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_pembelians.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($karyawan) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pembelians')) {
                        $actions .= '<a href="' . route('pembelians.show', $karyawan) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pembelians')) {
                        $actions .= '<a href="' . route('pembelians.edit', $karyawan) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pembelians')) {
                        $actions .= '<form action="' . route('pembelians.destroy', $karyawan) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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

        return view('dynamiccrud.create', compact('suppliers'), $pagedata);
    }

    public function createlanjut(Pembelian $pembelian): View
    {
        $supplier = Supplier::find($pembelian->supplier_id);

        $simpanpinjamsupplier = SimpanPinjamSupplier::where("supplier_id", $supplier->id)->first();

        $pagedata = $this->getPagedata();

        $data = $pembelian;

        return view('pembelians.createlanjut', compact('supplier', 'simpanpinjamsupplier', 'data'), $pagedata,);
    }

    public function store(Request $request): RedirectResponse
    {

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


        $pembelian = Pembelian::create($store_data);

        $simpanpinjamsupplier = SimpanPinjamSupplier::create([
            'supplier_id' => $store_data['supplier_id'],
            'pembelian_id' => $pembelian->id,
            'nominal' => $request->nominal,
            'keterangan' => 'ini keterangan'

        ]);

        if ($store_data['tipe_transaksi_pembelian'] === 'Titip') {
            return to_route('pembeliandetails.createtitip', $pembelian->id);
        }

        return to_route('pembeliandetails.createjual', $pembelian->id);
    }

    public function storelanjut(Pembelian $pembelian, Request $request): RedirectResponse
    {

        $store_data = [
            'metode_pembayaran' => $request->input('metode_pembayaran'),
            'tipe_pembayaran' => $request->input('tipe_pembayaran'),
            'nominal' => $request->input('nominal'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];



        $p = Pembelian::find($pembelian->id);
        $p->update([
            'metode_pembayaran' => $store_data['metode_pembayaran'],
            'tipe_pembayaran' => $store_data['tipe_pembayaran'],
        ]);




        $simpanpinjamsupplier = SimpanPinjamSupplier::find($pembelian->id)->update([
            'nominal' => $store_data['nominal'],
            'keterangan' => $store_data['keterangan']

        ]);


        return to_route('pembelians.index');
    }

    

    public function cetakNota($id)
    {
        $pembelian = Pembelian::find($id);
        $pembelian->details = PembelianDetail::where('pembelian_id', $id)->get();
        $pembelian->supplier = Supplier::find($pembelian->supplier_id);

        // dd($pembelian);

        // Opsional: Atur ukuran kertas (khusus nota thermal biasanya 80mm atau 58mm)
        // Jika kertas A4 gunakan 'a4', jika thermal gunakan array [0, 0, 226.77, 500] (80mm x sesuai panjang)
        $pdf = Pdf::loadView('exports.nota', compact('pembelian'))
            ->setPaper('a4', 'portrait');

        // Stream untuk melihat di browser, atau download() untuk langsung unduh
        return $pdf->download('Nota-' . $pembelian->kode_transaksi . '.pdf');
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

        return view('dynamiccrud.edit', compact('data'), $pagedata);
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
    public function destroy(Pembelian $karyawan): RedirectResponse
    {
        $karyawan->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pembelians.index')->with('status', 'Pembelian deleted successfully.');
    }
}
