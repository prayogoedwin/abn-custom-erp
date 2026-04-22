<?php

namespace App\Http\Controllers;

use App\Exports\PembelianDetailExport;
use App\Models\Pembelian;
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
                //     $query->where('pembeliandetails.nama_pembelian', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_pembeliandetails.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($pembelian) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pembeliandetails')) {
                        $actions .= '<a href="' . route('pembeliandetails.show', $pembelian) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pembeliandetails')) {
                        $actions .= '<a href="' . route('pembeliandetails.edit', $pembelian) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pembeliandetails')) {
                        $actions .= '<form action="' . route('pembeliandetails.destroy', $pembelian) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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


    public function createtitip($pembelian_id): View
    {
        $produks = Produk::where('isActive', true)->get();



        $pagedata = $this->getPagedata();
        $pagedata['pembelian_id'] = $pembelian_id;

        return view('pembeliandetails.createtitip', compact('produks'), $pagedata);
    }


    public function createjual($pembelian_id): View
    {
        $produks = Produk::where('isActive', true)->get();



        $pagedata = $this->getPagedata();
        $pagedata['pembelian_id'] = $pembelian_id;

        return view('pembeliandetails.createjual', compact('produks'), $pagedata);
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


        foreach ($request->produk_id as $index => $produk_id) {
            if (!empty($produk_id)) {
                // Simpan setiap item ke database
                PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'netto'        => $request->netto[$index] ?? 0,
                    'satuan'       => $request->satuan[$index] ?? "satuan",
                    'rendeman'     => $request->rendeman[$index] ?? null,
                    'created_by' => auth()->id(),
                ]);
            }
        }



        return to_route('pembelians.createlanjut', $store_data['pembelian_id']);
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


        foreach ($request->produk_id as $index => $produk_id) {
            if ($produk_id) {

                
                PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'netto'        => $request->netto[$index],
                    'satuan'       => $request->satuan[$index], // Nilai ini datang dari input hidden tadi
                    'rendeman'     => $request->rendeman[$index] ?? null,
                    'harga'     => $request->harga[$index] ?? null,
                    'harga_basis'     => $request->harga_basis[$index] ?? null,
                    'harga_basis_pembelian'     => $request->harga_basis_pembelian[$index] ?? null,
                    'harga_netto'     => $request->harga_netto[$index] ?? null,
                ]);
            }
        }




        return to_route('pembelians.createlanjut', $store_data['pembelian_id']);
    }




    public function show(PembelianDetail $pembelian): View
    {

        $data = $pembelian;


        $pagedata = $this->getPagedata();

       

        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edittitip(Pembelian $pembelian): View
    {
        $pembeliandetails = PembelianDetail::where('pembelian_id', $pembelian->id)->get();
        $produks = Produk::all();
        $data = $pembelian;

        $pagedata = $this->getPagedata();
        $pagedata['pembelian_id'] = $pembelian->id;

        // dd($pembeliandetails);

        return view('pembeliandetails.edittitip', compact('pembeliandetails', 'produks', 'data'), $pagedata);
    }

    public function editjual(Pembelian $pembelian): View
    {
        $pembeliandetails = PembelianDetail::where('pembelian_id', $pembelian->id)->get();
        $produks = Produk::all();
        $data = $pembelian;

        $pagedata = $this->getPagedata();
        $pagedata['pembelian_id'] = $pembelian->id;

        // dd($pembeliandetails);

        return view('pembeliandetails.editjual', compact('pembeliandetails', 'produks', 'data'), $pagedata);
    }

    public function titipupdate(Request $request): RedirectResponse 
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

        // dd($request->all());

        // Hapus semua dulu
        PembelianDetail::where('pembelian_id', $request->pembelian_id)->delete();


        foreach ($request->produk_id as $index => $produk_id) {
            if (!empty($produk_id)) {
                // Simpan ulang setiap item ke database
                PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'netto'        => $request->netto[$index] ?? 0,
                    'satuan'       => $request->satuan[$index] ?? "satuan",
                    'rendeman'     => $request->rendeman[$index] ?? null,

                    'updated_by' => auth()->id(),
                ]);
            }
        }


        return to_route('pembelians.createlanjut', $store_data['pembelian_id']);

    }

    public function jualupdate(Request $request): RedirectResponse 
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

        // dd($request->all());

        // Hapus semua dulu
        PembelianDetail::where('pembelian_id', $request->pembelian_id)->delete();


        foreach ($request->produk_id as $index => $produk_id) {
            if (!empty($produk_id)) {
                // Simpan ulang setiap item ke database
                PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'netto'        => $request->netto[$index] ?? 0,
                    'satuan'       => $request->satuan[$index] ?? "satuan",
                    'rendeman'     => $request->rendeman[$index] ?? null,
                    'harga'     => $request->harga[$index] ?? null,
                    'harga_basis'     => $request->harga_basis[$index] ?? null,
                    'harga_basis_pembelian'     => $request->harga_basis_pembelian[$index] ?? null,
                    'harga_netto'     => $request->harga_netto[$index] ?? null,

                    'updated_by' => auth()->id(),
                ]);
            }
        }


        return to_route('pembelians.createlanjut', $store_data['pembelian_id']);

    }



    //soft delete
    public function destroy(PembelianDetail $pembelian): RedirectResponse
    {
        $pembelian->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pembeliandetails.index')->with('status', 'PembelianDetail deleted successfully.');
    }
}
