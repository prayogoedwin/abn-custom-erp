<?php

namespace App\Http\Controllers;

use App\Exports\CashbonSupplierExport;
use App\Models\CashbonSupplier;
use App\Models\Supplier;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CashbonSupplierController extends Controller
{
    private function getPagedata()
    {
        // protected $fillable = [
        //     'supplier_id',
        //     'nominal_cashbon',
        //     'tipe',
        //     'keterangan',

        //     'created_by',
        //     'updated_by',
        //     'deleted_at',
        //     'deleted_by',
        // ];

        $suppliers = Supplier::where('deleted_at', null)->get();

        $pagedata = [
            'title' => 'Cashbon Supplier',
            'tablename' => 'cashbonsuppliers',
            'tableaction' => true,
            'columns' => [
                ['name' => 'supplier_id', 'value' => 'supplier',  'title' => 'Supplier', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$suppliers->map(function ($supplier) {
                        return ['value' => $supplier->id, 'label' => $supplier->nama];
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
        // dd($request->headers->all());
        // $cashbon_suppliers = CashbonSupplier::join('suppliers', 'cashbon_suppliers.supplier_id', '=', 'suppliers.id')
        //     ->select('cashbon_suppliers.*', 'suppliers.nama as supplier')
        //     ->get();
        // dd($cashbon_suppliers);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $cashbon_suppliers = CashbonSupplier::where('cashbon_suppliers.deleted_at', null)
                ->join('suppliers', 'cashbon_suppliers.supplier_id', '=', 'suppliers.id')
                ->select('cashbon_suppliers.*', 'suppliers.nama as supplier')
                ->get();
            // dd($cashbon_suppliers);

            return DataTables::of($cashbon_suppliers)




                ->addColumn('actions', function ($cashbonsupplier) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-cashbonsuppliers')) {
                        $actions .= '<a href="' . route('cashbonsuppliers.show', $cashbonsupplier) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-cashbonsuppliers')) {
                        $actions .= '<a href="' . route('cashbonsuppliers.edit', $cashbonsupplier) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-cashbonsuppliers')) {
                        $actions .= '<form action="' . route('cashbonsuppliers.destroy', $cashbonsupplier) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        // return Excel::download(new CashbonSupplierExport, 'cashbon_suppliers-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'supplier_id' => $request->input('supplier_id'),
            'nominal_cashbon' => $request->input('nominal_cashbon'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'supplier_id' => ['required', 'integer', 'max:255'],
            'nominal_cashbon' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $cashbonSupplier = CashbonSupplier::create($store_data);


        return to_route('cashbonsuppliers.index')->with('status', 'CashbonSupplier updated successfully.');
    }

    public function show(int $cashbonSupplier): View
    {

        $data = CashbonSupplier::find($cashbonSupplier);
        $data->supplier = $data->supplier->nama;

        // dd($data);


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe
        // dd($cashbonSupplier);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(int $cashbonSupplier): View
    {

        $data = CashbonSupplier::find($cashbonSupplier);


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, int $cashbonSupplier): RedirectResponse
    {
        // dd($request->all());
        $cashbonSupplier = CashbonSupplier::find($cashbonSupplier);



        // dd("current user id: " . $current_user_id);
        $store_data = [
            'supplier_id' => $request->input('supplier_id'),
            'nominal_cashbon' => $request->input('nominal_cashbon'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'supplier_id' => ['required', 'integer', 'max:255'],
            'nominal_cashbon' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $cashbonSupplier->update($store_data);


        // dd("cashbonSupplier updated: " . json_encode($cashbonSupplier));



        return to_route('cashbonsuppliers.index')->with('status', 'CashbonSupplier updated successfully.');
    }

    //soft delete
    public function destroy(int $cashbonSupplier): RedirectResponse
    {
        $cashbonSupplier = CashbonSupplier::find($cashbonSupplier);
        $cashbonSupplier->update(['deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('cashbonsuppliers.index')->with('status', 'CashbonSupplier deleted successfully.');
    }
}
