<?php

namespace App\Http\Controllers;

use App\Exports\Pihak3Export;
use App\Models\Pihak3;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class Pihak3Controller extends Controller
{
    private function getPagedata()
    {
        
        $pagedata = [
            'title' => 'Pihak3',
            'tablename' => 'pihak3s',

            'columns' => [
                ['name' => 'nama', 'value' => 'nama',  'title' => 'Nama Pihak3', 'type' => 'text', 'inform' => true, 'intable' => true],
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
            $pihak3s = Pihak3::where('pihak3s.isactive', true)
                ->get();
            // dd($pihak3s);

            return DataTables::of($pihak3s)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('pihak3s.nama_pihak3', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_pihak3s.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($pihak3) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pihak3s')) {
                        $actions .= '<a href="' . route('pihak3s.show', $pihak3) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pihak3s')) {
                        $actions .= '<a href="' . route('pihak3s.edit', $pihak3) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pihak3s')) {
                        $actions .= '<form action="' . route('pihak3s.destroy', $pihak3) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new Pihak3Export, 'pihak3s-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', compact('kategoris'), $pagedata);
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



        $pihak3 = Pihak3::create($store_data);
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

        return to_route('pihak3s.index')->with('status', 'Pihak3 updated successfully.');
    }

    public function show(Pihak3 $pihak3): View
    {
        
        $data = $pihak3;


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe



        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Pihak3 $pihak3): View
    {
       

        $data = $pihak3;

        

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Pihak3 $pihak3): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
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


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $pihak3->update($store_data);


        // dd("pihak3 updated: " . json_encode($pihak3));



        return to_route('pihak3s.index')->with('status', 'Pihak3 updated successfully.');
    }

    //soft delete
    public function destroy(Pihak3 $pihak3): RedirectResponse
    {
        $pihak3->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pihak3s.index')->with('status', 'Pihak3 deleted successfully.');
    }
}