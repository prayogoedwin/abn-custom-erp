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
    private function getPagedata(){
        $pagedata = [
            'title' => 'Stok',
            'tablename' => 'stoks',
            'tableaction' => false,
            'columns' => [
                ['name' => 'nama_produk', 'value' => 'nama_produk', 'title' => 'Produk', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'tipe_stok', 'value' => 'tipe_stok', 'title' => 'Tipe Stok', 'type' => 'text', 'inform' => false, 'intable' => true],
                ['name' => 'satuan', 'value' => 'satuan', 'title' => 'Satuan', 'type' => 'text', 'inform' => false, 'intable' => true],
                ['name' => 'stok', 'value' => 'stok', 'title' => 'Stok', 'type' => 'number', 'inform' => false, 'intable' => true],
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
                ->get();


            return DataTables::of($stoks)
                
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

    public function show(Stok $kategori): View
    {
        $pagedata = $this->getPagedata();

        return view('stoks.show', compact('kategori'), $pagedata);
    }

    public function edit(Stok $kategori): View
    {
        $pagedata = $this->getPagedata();

        $data = $kategori;

        return view('dynamiccrud.edit', compact('data'), $pagedata);

        
    }

    public function update(Request $request, Stok $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $kategori->update($validated);

        return to_route('stoks.index')->with('status', 'Stok updated successfully.');
    }

    public function destroy(Stok $kategori): RedirectResponse
    {
        $kategori->delete();

        return to_route('stoks.index')->with('status', 'Stok deleted successfully.');
    }
}
