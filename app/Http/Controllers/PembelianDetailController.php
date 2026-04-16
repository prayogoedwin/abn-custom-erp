<?php

namespace App\Http\Controllers;

use App\Exports\PembelianDetailExport;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\Stok;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PembelianDetailController extends Controller
{
    private function getPagedata()
    {
        //     'pembelian_id',
        //     'produk_id',
        //     'netto',
        //     'satuan',
        //     'rendeman',
        //     'bobot',
        //     'harga',
        //     'harga_basis',
        //     'harga_basis_pembelian',
        //     'harga_netto',

        $produks = Produk::where('isActive', true)->get();

        $pagedata = [
            'title' => 'PembelianDetail',
            'tablename' => 'pembeliandetails',
            'tableaction' => true,
            'columns' => [
                ['name' => 'pembelian_id', 'value' => 'pembelian_id',  'title' => 'pembelian_id', 'type' => 'text', 'inform' => false, 'inshow' => true, 'intable' => false],
                ['name' => 'produk_id', 'value' => 'produk', 'title' => 'Produk', 'type' => 'select', 'inform' => true, 'inshow' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$produks->map(function ($produk) {
                        return ['value' => $produk->id, 'label' => $produk->nama_produk];
                    })->toArray(),
                ]],
                ['name' => 'rendeman', 'value' => 'rendeman', 'title' => 'Rendeman', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'bobot', 'value' => 'bobot', 'title' => 'bobot', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'netto', 'value' => 'netto', 'title' => 'Netto', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'satuan', 'value' => 'satuan', 'title' => 'satuan', 'type' => 'string', 'inform' => true, 'inshow' => true, 'intable' => true],

            ],
        ];

        // dd($pagedata);

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        if ($request->ajax()) {
            // dd('masuk ajax');
            $pembeliandetails = PembelianDetail::where('pembeliandetails.isactive', true)
                ->get();
            // dd($pembeliandetails);

            return DataTables::of($pembeliandetails)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('pembeliandetails.nama_pihak3', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_pembeliandetails.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($pihak3) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pembeliandetails')) {
                        $actions .= '<a href="' . route('pembeliandetails.show', $pihak3) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pembeliandetails')) {
                        $actions .= '<a href="' . route('pembeliandetails.edit', $pihak3) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pembeliandetails')) {
                        $actions .= '<form action="' . route('pembeliandetails.destroy', $pihak3) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new PembelianDetailExport, 'pembeliandetails-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $produks = Produk::where('isActive', true)->get();

        $pagedata = $this->getPagedata();

        return view('pembeliandetails.create', compact('produks'), $pagedata);
    }

    public function createtitip($pembelian_id): View
    {
        $produks = Produk::where('isActive', true)->get();



        $pagedata = $this->getPagedata();
        $pagedata['pembelian_id'] = $pembelian_id;

        return view('pembeliandetails.createtitip', compact('produks'), $pagedata);
    }


    public function titipstore(Request $request): RedirectResponse
    {
        $store_data = [
            'pembelian_id' => $request->input('pembelian_id'),
            'produk_id' => $request->input('produk_id'),
            'netto' => $request->input('netto'),
            'satuan' => $request->input('satuan'),
            'rendeman' => $request->input('rendeman'),
            'bobot' => $request->input('bobot'),
            'harga' => $request->input('harga'),
            'harga_basis' => $request->input('harga_basis'),
            'harga_basis_pembelian' => $request->input('harga_basis_pembelian'),
            'harga_netto' => $request->input('harga_netto'),

            'created_by' => auth()->id(),
        ];

        //      'pembelian_id',
        //     'produk_id',
        //     'netto',
        //     'satuan',
        //     'rendeman',
        //     'bobot',
        //     'harga',
        //     'harga_basis',
        //     'harga_basis_pembelian',
        //     'harga_netto',


        $store_data['satuan'] = Produk::where('id', $request->produk_id)->value('satuan');
        // dd($store_data);



        $pembelian = PembelianDetail::create($store_data);


        return to_route('pembelians.index')->with('status', 'Pembelian updated successfully.');
    }
    public function jualstore(Request $request): RedirectResponse
    {
        $store_data = [
            'pembelian_id' => $request->input('pembelian_id'),
            'produk_id' => $request->input('produk_id'),
            'netto' => $request->input('netto'),
            'satuan' => $request->input('satuan'),
            'rendeman' => $request->input('rendeman'),
            'bobot' => $request->input('bobot'),
            'harga' => $request->input('harga'),
            'harga_basis' => $request->input('harga_basis'),
            'harga_basis_pembelian' => $request->input('harga_basis_pembelian'),
            'harga_netto' => $request->input('harga_netto'),

            'created_by' => auth()->id(),
        ];

        //      'pembelian_id',
        //     'produk_id',
        //     'netto',
        //     'satuan',
        //     'rendeman',
        //     'bobot',
        //     'harga',
        //     'harga_basis',
        //     'harga_basis_pembelian',
        //     'harga_netto',


        $store_data['satuan'] = Produk::where('id', $request->produk_id)->value('satuan');
        // dd($store_data);


        Stok::create([
            'produk_id' => $store_data['produk_id'],
            'tipe_stok' => 'Masuk',
            'satuan' => $store_data['satuan'],
            'stok' => $store_data['netto'],
        ]);

        
        $pembelian = PembelianDetail::create($store_data);


        return to_route('pembelians.index')->with('status', 'Pembelian updated successfully.');
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



        $pihak3 = PembelianDetail::create($store_data);
        // // log stok change
        // Stok::create([
        //     'pihak3_id' => $pihak3->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'pihak3_id' => $pihak3->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('pembeliandetails.index')->with('status', 'PembelianDetail updated successfully.');
    }

    public function show(PembelianDetail $pihak3): View
    {

        $data = $pihak3;


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe



        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(PembelianDetail $pihak3): View
    {


        $data = $pihak3;



        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, PembelianDetail $pihak3): RedirectResponse
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

        $pihak3->update($store_data);


        // dd("pihak3 updated: " . json_encode($pihak3));



        return to_route('pembeliandetails.index')->with('status', 'PembelianDetail updated successfully.');
    }

    //soft delete
    public function destroy(PembelianDetail $pihak3): RedirectResponse
    {
        $pihak3->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pembeliandetails.index')->with('status', 'PembelianDetail deleted successfully.');
    }
}
