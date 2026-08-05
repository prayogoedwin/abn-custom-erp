<?php

namespace App\Http\Controllers;

use App\Exports\HistoryHargaBasisExport;
use App\Models\HistoryHargaBasis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class HistoryHargaBasisController extends Controller
{
    private function getPagedata(){
        $pagedata = [
            'title' => 'HistoryHargaBasis',
            'tablename' => 'history_harga_bases',
            'tableaction' => false,
            'columns' => [
                ['name' => 'tanggal', 'value' => 'tanggal', 'title' => 'Tanggal', 'type' => 'number', 'inform' => false, 'intable' => true],
                ['name' => 'nama_produk', 'value' => 'nama_produk', 'title' => 'Produk', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'satuan', 'value' => 'satuan', 'title' => 'Satuan', 'type' => 'text', 'inform' => false, 'intable' => true],
                ['name' => 'harga_basis', 'value' => 'harga_basis', 'title' => 'Harga', 'type' => 'text', 'inform' => false, 'intable' => true],
                
            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        
        if ($request->ajax()) {
            $history_harga_bases = HistoryHargaBasis::join('produks', 'history_harga_bases.produk_id', '=', 'produks.id')
                // Select everything from karyawan, and specific fields from users
                ->select('history_harga_bases.*', 'produks.nama_produk');
                


            return DataTables::of($history_harga_bases)
                
                ->make(true);
        }

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.index', $pagedata);
    }

    public function export()
    {
        return Excel::download(new HistoryHargaBasisExport, 'history_harga_bases-' . date('Y-m-d') . '.xlsx');
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

        HistoryHargaBasis::create($validated);

        return to_route('history_harga_bases.index')->with('status', 'HistoryHargaBasis created successfully.');
    }

    public function show(HistoryHargaBasis $historyhargabasis): View
    {
        $pagedata = $this->getPagedata();

        return view('history_harga_bases.show', compact('historyhargabasis'), $pagedata);
    }

    public function edit(HistoryHargaBasis $historyhargabasis): View
    {
        $pagedata = $this->getPagedata();

        $data = $historyhargabasis;

        return view('dynamiccrud.edit', compact('data'), $pagedata);

        
    }

    public function update(Request $request, HistoryHargaBasis $historyhargabasis): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $historyhargabasis->update($validated);

        return to_route('history_harga_bases.index')->with('status', 'HistoryHargaBasis updated successfully.');
    }

    public function destroy(HistoryHargaBasis $historyhargabasis): RedirectResponse
    {
        $historyhargabasis->delete();

        return to_route('history_harga_bases.index')->with('status', 'HistoryHargaBasis deleted successfully.');
    }
}


