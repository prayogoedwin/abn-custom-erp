<?php

namespace App\Http\Controllers;

use App\Exports\CashbonSupplierExport;
use App\Models\CashbonPihak3;
use App\Models\Pihak3;
use App\Models\Supplier;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CashbonPihak3Controller extends Controller
{
    private function getPagedata()
    {
        

        $pihak3s = Pihak3::where('deleted_at', null)->get();

        $pagedata = [
            'title' => 'Cashbon Pihak 3',
            'tablename' => 'cashbonpihak3s',
            'tableaction' => true,
            'columns' => [
                ['name' => 'pihak3_id', 'value' => 'pihak3',  'title' => 'Pihak 3', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$pihak3s->map(function ($pihak3) {
                        return ['value' => $pihak3->id, 'label' => $pihak3->nama];
                    })->toArray(),
                ]],
                ['name' => 'nominal_cashbon', 'value' => 'nominal_cashbon', 'title' => 'Nominal Cashbon', 'type' => 'number', 'inform' => true, 'intable' => true],
                ['name' => 'tipe', 'value' => 'tipe',  'title' => 'Tipe', 'type' => 'select', 'inform' => true, 'intable' => false, 'options' => [
                    ['value' => 'Cash', 'label' => 'Cash'],
                    ['value' => 'Transfer', 'label' => 'Transfer'],

                ]],

                ['name' => 'keterangan', 'value' => 'keterangan', 'title' => 'Keterangan', 'type' => 'text', 'inform' => true, 'intable' => true],

            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        
        if ($request->ajax()) {
            // dd('masuk ajax');
            $cashbon_pihak3s = CashbonPihak3::with('pihak3')
                ;
            // dd($cashbon_pihak3s);

            return DataTables::of($cashbon_pihak3s)
                ->editColumn('nominal_cashbon', function ($cashbonpihak3) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($cashbonpihak3->nominal_cashbon, 0, ',', '.');
                })

                ->addColumn('pihak3', function ($cashbonpihak3) {
                    return $cashbonpihak3->pihak3->nama;
                })



                ->addColumn('actions', function ($cashbonpihak3) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-cashbonpihak3s')) {
                        $actions .= '<a href="' . route('cashbonpihak3s.show', $cashbonpihak3) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-cashbonpihak3s')) {
                        $actions .= '<a href="' . route('cashbonpihak3s.edit', $cashbonpihak3) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-cashbonpihak3s')) {
                        $actions .= '<form action="' . route('cashbonpihak3s.destroy', $cashbonpihak3) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        // TODO:
        // return Excel::download(new CashbonPihak3Export, 'cashbon_pihak3s-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'pihak3_id' => $request->input('pihak3_id'),
            'nominal_cashbon' => $request->input('nominal_cashbon'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'pihak3_id' => ['required', 'integer', 'max:255'],
            'nominal_cashbon' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $cashbonPihak3 = CashbonPihak3::create($store_data);


        return to_route('cashbonpihak3s.index')->with('status', 'CashbonPihak3 updated successfully.');
    }

    public function show(int $cashbonPihak3): View
    {

        $data = CashbonPihak3::find($cashbonPihak3);
        $data->pihak3 = $data->pihak3->nama;

        // dd($data);


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe
        // dd($cashbonPihak3);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(int $cashbonPihak3): View
    {

        $data = CashbonPihak3::find($cashbonPihak3);


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, int $cashbonPihak3): RedirectResponse
    {
        // dd($request->all());
        $cashbonPihak3 = CashbonPihak3::find($cashbonPihak3);



        // dd("current user id: " . $current_user_id);
        $store_data = [
            'pihak3_id' => $request->input('pihak3_id'),
            'nominal_cashbon' => $request->input('nominal_cashbon'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'pihak3_id' => ['required', 'integer', 'max:255'],
            'nominal_cashbon' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $cashbonPihak3->update($store_data);


        // dd("cashbonPihak3 updated: " . json_encode($cashbonPihak3));



        return to_route('cashbonpihak3s.index')->with('status', 'CashbonPihak3 updated successfully.');
    }

    //soft delete
    public function destroy(int $cashbonPihak3): RedirectResponse
    {
        $cashbonPihak3 = CashbonPihak3::find($cashbonPihak3);
        $cashbonPihak3->update(['deleted_by' => auth()->id()]);
        $cashbonPihak3->delete();


        return to_route('cashbonpihak3s.index')->with('status', 'CashbonPihak3 deleted successfully.');
    }
}
