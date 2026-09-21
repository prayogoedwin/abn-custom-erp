<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Models\Customer;
use App\Exports\PengeluaranExport;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;


class KategoriPengeluaranController extends Controller
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

        


        $kategori_pengeluaran = KategoriPengeluaran::where('deleted_at', null)->get();
        // dd($suppliers);

        $pagedata = [
            'title' => 'Kategori Pengeluaran',
            'tablename' => 'kategori-pengeluarans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'nama_kategori', 'value' => 'nama_kategori',  'title' => 'Nama Kategori Pengeluaran', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],
                
                ['name' => 'created_at', 'value' => 'created_at',  'title' => 'Created At', 'type' => 'date', 'inform' => true, 'inshow' => true, 'intable' => true],

            ],
        ];

        return $pagedata;
    }

    

    public function index(Request $request)
    {
        

        $pagedata = $this->getPagedata();

        return view('kategoripengeluarans.index', $pagedata);
    }

    public function indexTable(Request $request)
    {
        if ($request->ajax()) {
            $data = KategoriPengeluaran::where('deleted_at', null)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('actions', function ($pengeluaran) {
                    $actions = '';

                    //

                    if (auth()->user()->hasPermission('delete-kategori-pengeluarans')) {
                        $actions .= '<form action="' . route('kategori-pengeluarans.destroy', $pengeluaran) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    

    public function create(): View
    {
        $customers = Customer::where('deleted_at', null)->get();

        $pagedata = $this->getPagedata();

        return view('kategoripengeluarans.create', compact('customers'), $pagedata);
    }


    public function store(Request $request): RedirectResponse
    {

        $store_data = [
            'nama_kategori' => $request->input('nama_kategori'),
            'created_by' => auth()->id(),
        ];

        //$store_data['no_transaksi'] = terjadi di model



        $validate = Validator::make($store_data, [
            'nama_kategori' => ['required', 'string', 'max:255'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $kategori_pengeluaran = KategoriPengeluaran::create($store_data);



        return to_route('kategori-pengeluarans.index')->with('status', 'Kategori Pengeluaran created successfully.');
    }


    public function show(int $id)
    {
        //
    }

    

    public function edit(KategoriPengeluaran $kategori_pengeluaran)
    {

        //
    }

    public function update(Request $request, KategoriPengeluaran $kategori_pengeluaran)
    {
        //
    }

    //soft delete
    public function destroy(KategoriPengeluaran $kategori_pengeluaran): RedirectResponse
    {
        $kategori_pengeluaran->update(['deleted_by' => auth()->id()]);
        $kategori_pengeluaran->delete();
    


        return to_route('kategori-pengeluarans.index')->with('status', 'Kategori Pengeluaran deleted successfully.');
    }
}


