<?php

namespace App\Http\Controllers;

use App\Exports\SupplierExport;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    private function getPagedata()
    {
        

        $pagedata = [
            'title' => 'Supplier',
            'tablename' => 'suppliers',
            'tableaction' => true,
            'columns' => [
                ['name' => 'nama', 'value' => 'nama',  'title' => 'Nama Supplier', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'kontak', 'value' => 'kontak', 'title' => 'Kontak', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'alamat', 'value' => 'alamat', 'title' => 'Alamat', 'type' => 'text', 'inform' => true, 'intable' => true],
                
            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        if ($request->ajax()) {
            // dd('masuk ajax');
            $suppliers = Supplier::where('suppliers.isactive', true)
                ->get();
            // dd($suppliers);

            return DataTables::of($suppliers)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('suppliers.nama_supplier', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_suppliers.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($supplier) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-suppliers')) {
                        $actions .= '<a href="' . route('suppliers.show', $supplier) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-suppliers')) {
                        $actions .= '<a href="' . route('suppliers.edit', $supplier) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-suppliers')) {
                        $actions .= '<form action="' . route('suppliers.destroy', $supplier) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new SupplierExport, 'suppliers-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        

        $pagedata = $this->getPagedata();
        
        

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'nama' => $request->input('nama'),
            'kontak' => $request->input('kontak'),
            'alamat' => $request->input('alamat'),
            
            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama' => ['required', 'string', 'max:255'],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],
            
            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $supplier = Supplier::create($store_data);
        // // log stok change
        // Stok::create([
        //     'supplier_id' => $supplier->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'supplier_id' => $supplier->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('suppliers.index')->with('status', 'Supplier updated successfully.');
    }

    public function show(Supplier $supplier): View
    {
        // Eager load semua relasi yang dibutuhkan oleh view detail supplier
        $supplier->load([
            'cashbons', 
            'cashbonspembayaran',
            // Memuat riwayat stok titipan diurutkan dari yang terbaru, beserta data produknya
            'titipSuppliers',
            'ambilSuppliers',
            'stokTitipans'
        ]);

        
        /*
        $supplier->load([
            'titipSuppliers',
            'ambilSuppliers'
        ]);
        */
        $pembelians = Pembelian::where('supplier_id', $supplier->id)->get();

        $title = 'Supplier';
        $tablename = 'suppliers';

        return view('suppliers.show', compact('supplier', 'title', 'tablename', 'pembelians'));
    }

    public function showTable(Supplier $supplier)
    {
        if (request()->ajax()) {
            // riwayat transaksi dengan supplier, 
            $data = Pembelian::where('supplier_id', $supplier->id)
                ->select('pembelians.*');
                
            return datatables()->of($data)
                ->addColumn('no_transaksi', function ($pembelian) {
                    return '<a href="' . route('pembelians.show', $pembelian->id) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">' . $pembelian->no_transaksi . '</a>';
                })
                ->addColumn('status', function ($row) {
                    return $row->status_pembayaran;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })
                
                ->rawColumns(['no_transaksi'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function edit(Supplier $supplier): View
    {
       

        $data = $supplier;

        

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nama' => $request->input('nama'),
            'kontak' => $request->input('kontak'),
            'alamat' => $request->input('alamat'),
            
            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama' => ['required', 'string', 'max:255'],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],
            
            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $supplier->update($store_data);


        // dd("supplier updated: " . json_encode($supplier));



        return to_route('suppliers.index')->with('status', 'Supplier updated successfully.');
    }

    //soft delete
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('suppliers.index')->with('status', 'Supplier deleted successfully.');
    }
}
