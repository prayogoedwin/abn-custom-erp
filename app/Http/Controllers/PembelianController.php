<?php

namespace App\Http\Controllers;

use App\Exports\PembelianExport;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\User;
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
                ['name' => 'tipe_transaksi_pembelian', 'value' => 'tipe_transaksi_pembelian',  'title' => 'Tipe Transaksi Pembelian', 'type' => 'select', 'inform' => true, 'inshow' => true, 'intable' => true, 'options' => [
                    ['value' => 'Titip', 'label' => 'Titip'],
                    ['value' => 'Jual', 'label' => 'Jual'],
                ]],
                ['name' => 'total_nominal_pembelian', 'value' => 'total_nominal_pembelian',  'title' => 'Total Nominal Pembelian', 'type' => 'rupiah', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'total_nominal_terbayar', 'value' => 'total_nominal_pembelian',  'title' => 'Total Nominal Terbayar', 'type' => 'rupiah', 'inform' => true, 'inshow' => true, 'intable' => true],
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
        //     ->select('pembelians.*', 'suppliers.nama as supplier_name')
        //     ->where('pembelians.isactive', true)
        //     ->get();
        // dd($pembelians);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $pembelians = Pembelian::join('suppliers', 'pembelians.supplier_id', '=', 'suppliers.id')
                // Select everything from karyawan, and specific fields from users
                ->select('pembelians.*', 'suppliers.nama as supplier_name')
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

        return view('dynamiccrud.create',compact('suppliers'), $pagedata);
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


        // dd($store_data);


        Pembelian::create($store_data);
        // // log stok change
        // Stok::create([
        //     'karyawan_id' => $karyawan->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'karyawan_id' => $karyawan->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('pembeliandetails.create');
    }

    public function show(Pembelian $karyawan): View
    {
        $karyawan->email = User::find($karyawan->user_id)->email;

        $data = $karyawan;


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe

        // dd($data);

        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Pembelian $karyawan): View
    {
        $karyawan->email = User::find($karyawan->user_id)->email;

        $data = $karyawan;


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Pembelian $karyawan): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'kontak' => $request->input('kontak'),
            'alamat' => $request->input('alamat'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::default()],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }

        $user = User::find($karyawan->user_id);

        $user->update(
            [
                'name' => $store_data['nama'],
                'email' => $store_data['email'],
            ]
        );

        if (! empty($store_data['password'])) {
            $user->update([
                'password' => Hash::make($store_data['password']),
            ]);
        }

        $karyawan->update($store_data);



        // dd("karyawan updated: " . json_encode($karyawan));



        return to_route('pembelians.index')->with('status', 'Pembelian updated successfully.');
    }

    //soft delete
    public function destroy(Pembelian $karyawan): RedirectResponse
    {
        $karyawan->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pembelians.index')->with('status', 'Pembelian deleted successfully.');
    }
}
