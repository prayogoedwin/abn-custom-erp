<?php

namespace App\Http\Controllers;

use App\Exports\DinamisVariableExport;
use App\Models\DinamisVariable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DinamisVariableController extends Controller
{
    private function getPagedata()
    {

        $pagedata = [
            'title' => 'Variable Dinamis',
            'tablename' => 'dinamisvariables',
            'tableaction' => true,
            'columns' => [
                ['name' => 'nama_variable', 'value' => 'nama_variable',  'title' => 'Nama Variable Dinamis', 'type' => 'text', 'inform' => true, 'intable' => true],

                ['name' => 'jenis', 'value' => 'jenis', 'title' => 'Jenis', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [

                    ['value' => 'Persen', 'label' => 'Persen'],
                    ['value' => 'Nominal', 'label' => 'Nominal'],

                ]],
                ['name' => 'keterangan', 'value' => 'keterangan',  'title' => 'Keterangan', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'variable_value', 'value' => 'variable_value',  'title' => 'Value', 'type' => 'number', 'inform' => true, 'intable' => true],


            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        
        // dd($request->headers->all());
        if ($request->ajax()) {
            // dd('masuk ajax');
            $dinamisvariables = DinamisVariable::where('isactive', true)
                ->get();
            // dd($dinamisvariables);

            return DataTables::of($dinamisvariables)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('dinamisvariables.nama_dinamisvariable', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_dinamisvariables.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($dinamisvariable) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-dinamisvariables')) {
                        $actions .= '<a href="' . route('dinamisvariables.show', $dinamisvariable) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-dinamisvariables')) {
                        $actions .= '<a href="' . route('dinamisvariables.edit', $dinamisvariable) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-dinamisvariables')) {
                        $actions .= '<form action="' . route('dinamisvariables.destroy', $dinamisvariable) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new DinamisVariableExport, 'dinamisvariables-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'nama_variable' => $request->input('nama_variable'),
            'jenis' => $request->input('jenis'),
            'variable_value' => $request->input('variable_value'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama_variable' => ['required', 'string', 'max:255'],
            'jenis' => ['required'],
            'variable_value' => ['required', 'integer'],
            'keterangan' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $dinamisvariable = DinamisVariable::create($store_data);
        // // log stok change
        // Stok::create([
        //     'dinamisvariable_id' => $dinamisvariable->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'dinamisvariable_id' => $dinamisvariable->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('dinamisvariables.index')->with('status', 'Variable Dinamis updated successfully.');
    }

    public function show(DinamisVariable $dinamisvariable): View
    {

        $data = $dinamisvariable;


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe



        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(DinamisVariable $dinamisvariable): View
    {


        $data = $dinamisvariable;



        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, DinamisVariable $dinamisvariable): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nama_variable' => $request->input('nama_variable'),
            'jenis' => $request->input('jenis'),
            'variable_value' => $request->input('variable_value'),
            'keterangan' => $request->input('keterangan'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama_variable' => ['required', 'string', 'max:255'],
            'jenis' => ['required'],
            'variable_value' => ['required', 'integer',],
            'keterangan' => ['required', 'string', 'max:50'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $dinamisvariable->update($store_data);


        // dd("dinamisvariable updated: " . json_encode($dinamisvariable));



        return to_route('dinamisvariables.index')->with('status', 'Variable Dinamis updated successfully.');
    }

    //soft delete
    public function destroy(DinamisVariable $dinamisvariable): RedirectResponse
    {
        $dinamisvariable->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('dinamisvariables.index')->with('status', 'Variable Dinamis deleted successfully.');
    }
}
