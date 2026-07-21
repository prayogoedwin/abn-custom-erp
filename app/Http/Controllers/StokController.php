<?php

namespace App\Http\Controllers;

use App\Exports\StokExport;
use App\Models\Stok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    private function getPagedata()
    {
        $pagedata = [
            'title' => 'Stok',
            'tablename' => 'stoks',
            'tableaction' => true,
            'canCreate' => false,
            'columns' => [
                ['name' => 'nama_produk', 'value' => 'nama_produk', 'title' => 'Produk', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'tipe_stok', 'value' => 'tipe_stok', 'title' => 'Tipe Stok', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'satuan', 'value' => 'satuan', 'title' => 'Satuan', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'stok', 'value' => 'stok', 'title' => 'Stok', 'type' => 'number', 'inform' => true, 'intable' => true],
            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stoks = Stok::join('produks', 'stoks.produk_id', '=', 'produks.id')
                // Select everything from karyawan, and specific fields from users
                ->select('stoks.*', 'produks.nama_produk')
                ->orderBy('stoks.id', 'desc');


            return DataTables::of($stoks)
                ->addColumn('actions', function ($stok) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-produks')) {
                        $actions .= '<a href="' . route('stoks.show', $stok) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">Detail</a>';
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
        return Excel::download(new StokExport, 'stoks-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        Stok::create($validated);

        return to_route('stoks.index')->with('status', 'Stok created successfully.');
    }

    public function show(Stok $stok): View
    {
        $pagedata = $this->getPagedata();

        $data = $stok;
        $data->nama_produk = $stok->produk->nama_produk;

        // dd($data);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Stok $stok): View
    {
        $pagedata = $this->getPagedata();

        $data = $stok;

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Stok $stok): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $stok->update($validated);

        return to_route('stoks.index')->with('status', 'Stok updated successfully.');
    }

    public function destroy(Stok $stok): RedirectResponse
    {
        $stok->delete();

        return to_route('stoks.index')->with('status', 'Stok deleted successfully.');
    }
}
