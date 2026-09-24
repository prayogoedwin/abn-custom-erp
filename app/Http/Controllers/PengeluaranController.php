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


class PengeluaranController extends Controller
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
            'title' => 'Pengeluaran',
            'tablename' => 'pengeluarans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'nama_pengeluaran', 'value' => 'nama_pengeluaran',  'title' => 'Nama Pengeluaran', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true, 'required' => true],
                ['name' => 'kategori_pengeluaran_id', 'value' => 'kategori_pengeluaran',  'title' => 'Kategori Pengeluaran', 'type' => 'select', 'inform' => true, 'intable' => true, 'required' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$kategori_pengeluaran->map(function ($kategori) {
                        return ['value' => $kategori->id, 'label' => $kategori->nama_kategori];
                    })->toArray(),
                ]],
                ['name' => 'tanggal', 'value' => 'tanggal',  'title' => 'Tanggal', 'type' => 'date', 'inform' => true, 'inshow' => true, 'intable' => true, 'required' => true],
                ['name' => 'nominal', 'value' => 'nominal',  'title' => 'Nominal', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => true, 'required' => true],
                ['name' => 'metode_pembayaran', 'value' => 'metode_pembayaran',  'title' => 'Metode Pembayaran', 'type' => 'select', 'inform' => true, 'intable' => true, 'required' => false, 'options' => [
                    ['value' => 'Tunai', 'label' => 'Cash'],
                    ['value' => 'Transfer', 'label' => 'Transfer'],
                ]],
                ['name' => 'keterangan', 'value' => 'keterangan',  'title' => 'Keterangan', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true, 'required' => false],

            ],
        ];

        return $pagedata;
    }

    

    public function index(Request $request)
    {
        

        $pagedata = $this->getPagedata();

        return view('pengeluarans.index', $pagedata);
    }

    public function indexTable(Request $request)
    {
        if ($request->ajax()) {
            $data = Pengeluaran::where('deleted_at', null)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kategori_pengeluaran', function ($row) {
                    return $row->kategoriPengeluaran->nama_kategori ?? '';
                })
                
                // ->editColumn('tanggal', function ($row) {
                //     return $row->tanggal->translatedFormat('d M Y');
                // })
                ->editColumn('nominal', function ($row) {
                    return '<div style="text-align: right;">' . number_format($row->nominal, 0, ',', '.') . '</div>';
                })
                ->editColumn('metode_pembayaran', function ($row) {
                    return $row->metode_pembayaran;
                })
                ->addColumn('actions', function ($pengeluaran) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pengeluarans')) {
                        $actions .= '<a href="' . route('pengeluarans.show', $pengeluaran) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pengeluarans')) {
                        $actions .= '<a href="' . route('pengeluarans.edit', $pengeluaran) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pengeluarans')) {
                        $actions .= '<form action="' . route('pengeluarans.destroy', $pengeluaran) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions', 'nominal'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function export()
    {
        return Excel::download(new PengeluaranExport, 'pengeluarans-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $customers = Customer::where('deleted_at', null)->get();

        $pagedata = $this->getPagedata();

        return view('pengeluarans.create', compact('customers'), $pagedata);
    }


    public function store(Request $request): RedirectResponse
    {

        $store_data = [
            'nama_pengeluaran' => $request->input('nama_pengeluaran'),
            'kategori_pengeluaran_id' => $request->input('kategori_pengeluaran_id'),
            'tanggal' => $request->input('tanggal'),
            'nominal' => $request->input('nominal'),
            'metode_pembayaran' => $request->input('metode_pembayaran'),
            'keterangan' => $request->input('keterangan'),
            'created_by' => auth()->id(),
        ];

        //$store_data['no_transaksi'] = terjadi di model



        $validate = Validator::make($store_data, [
            'nama_pengeluaran' => ['required', 'string', 'max:255'],
            'kategori_pengeluaran_id' => ['required', 'integer'],
            'tanggal' => ['required', 'date'],
            'nominal' => ['required', 'numeric'],
            'metode_pembayaran' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $pengeluaran = Pengeluaran::create($store_data);



        return to_route('pengeluarans.index', $pengeluaran->id)->with('status', 'Pengeluaran created successfully.');
    }


    public function show(int $id): View
    {
        $pengeluaran = Pengeluaran::find($id);

        $pagedata = $this->getPagedata();



        return view('pengeluarans.show', compact('pengeluaran'), $pagedata);
    }

    

    public function edit(Pengeluaran $pengeluaran): View
    {

        $data = $pengeluaran;


        $pagedata = $this->getPagedata();

        return view('pengeluarans.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Pengeluaran $pengeluaran): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nama_pengeluaran' => $request->input('nama_pengeluaran'),
            'kategori_pengeluaran_id' => $request->input('kategori_pengeluaran_id'),
            'tanggal' => $request->input('tanggal'),
            'nominal' => $request->input('nominal'),
            'metode_pembayaran' => $request->input('metode_pembayaran'),
            'keterangan' => $request->input('keterangan'),
            'updated_by' => auth()->id(),
        ];

        //$store_data['no_transaksi'] = terjadi di model



        $validate = Validator::make($store_data, [
            'nama_pengeluaran' => ['required', 'string', 'max:255'],
            'kategori_pengeluaran_id' => ['required', 'integer'],
            'tanggal' => ['required', 'date'],
            'nominal' => ['required', 'numeric'],
            'metode_pembayaran' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $pengeluaran->update($store_data);




        return to_route('pengeluarans.index', $pengeluaran->id)->with('status', 'Pengeluaran updated successfully.');
    }

    //soft delete
    public function destroy(Pengeluaran $pengeluaran): RedirectResponse
    {
        $pengeluaran->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pengeluarans.index')->with('status', 'Pengeluaran deleted successfully.');
    }
}


