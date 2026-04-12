<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KategoriExport;

class KategoriProdukController extends Controller
{
    private function getPagedata(){
        $pagedata = [
            'title' => 'Kategori',
            'tablename' => 'kategoris',
            'columns' => [
                ['name' => 'nama', 'value' => 'nama', 'title' => 'Nama Kategori', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'created_at', 'value' => 'created_at', 'title' => 'Dibuat pada', 'type' => 'text', 'inform' => false, 'intable' => true],

                
            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kategoris = KategoriProduk::get();


            return DataTables::of($kategoris)
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })


                ->addColumn('actions', function ($kategori) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-kategoris')) {
                        $actions .= '<a href="' . route('kategoris.show', $kategori) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-kategoris')) {
                        $actions .= '<a href="' . route('kategoris.edit', $kategori) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-kategoris')) {
                        $actions .= '<form action="' . route('kategoris.destroy', $kategori) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new KategoriExport, 'kategoris-' . date('Y-m-d') . '.xlsx');
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

        KategoriProduk::create($validated);

        return to_route('kategoris.index')->with('status', 'Kategori created successfully.');
    }

    public function show(KategoriProduk $kategori): View
    {
        $pagedata = $this->getPagedata();

        return view('kategoris.show', compact('kategori'), $pagedata);
    }

    public function edit(KategoriProduk $kategori): View
    {
        $pagedata = $this->getPagedata();

        $data = $kategori;

        return view('dynamiccrud.edit', compact('data'), $pagedata);

        
    }

    public function update(Request $request, KategoriProduk $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $kategori->update($validated);

        return to_route('kategoris.index')->with('status', 'Kategori updated successfully.');
    }

    public function destroy(KategoriProduk $kategori): RedirectResponse
    {
        $kategori->delete();

        return to_route('kategoris.index')->with('status', 'Kategori deleted successfully.');
    }
}
