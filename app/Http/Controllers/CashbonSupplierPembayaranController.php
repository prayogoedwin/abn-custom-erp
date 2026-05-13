<?php

namespace App\Http\Controllers;

use App\Exports\CashbonSupplierPembayaranExport;
use App\Models\CashbonSupplierPembayaran;
use App\Models\Supplier;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CashbonSupplierPembayaranController extends Controller
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
            'title' => 'Cashbon Supplier Pembayaran',
            'tablename' => 'cashbonsupplierpembayarans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'supplier_id', 'value' => 'supplier',  'title' => 'Supplier', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$suppliers->map(function ($supplier) {
                        return ['value' => $supplier->id, 'label' => $supplier->nama];
                    })->toArray(),
                ]],
                ['name' => 'nominal_bayar', 'value' => 'nominal_bayar', 'title' => 'Nominal Pembayaran', 'type' => 'number', 'inform' => true, 'intable' => true],
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
        // $cashbon_supplier_pembayarans = CashbonSupplierPembayaran::where('cashbon_supplier_pembayarans.deleted_at', null)
        //     ->join('suppliers', 'cashbon_supplier_pembayarans.supplier_id', '=', 'suppliers.id')
        //     ->select('cashbon_supplier_pembayarans.*', 'suppliers.nama as supplier')
        //     ->get();
        // dd($cashbon_supplier_pembayarans);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $cashbon_supplier_pembayarans = CashbonSupplierPembayaran::where('cashbon_supplier_pembayarans.deleted_at', null)
                ->join('suppliers', 'cashbon_supplier_pembayarans.supplier_id', '=', 'suppliers.id')
                ->select('cashbon_supplier_pembayarans.*', 'suppliers.nama as supplier')
                ->get();
            // dd($cashbon_supplier_pembayarans);

            return DataTables::of($cashbon_supplier_pembayarans)




                ->addColumn('actions', function ($cashbonsupplier) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-cashbonsupplierpembayarans')) {
                        $actions .= '<a href="' . route('cashbonsupplierpembayarans.show', $cashbonsupplier) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-cashbonsupplierpembayarans')) {
                        $actions .= '<a href="' . route('cashbonsupplierpembayarans.edit', $cashbonsupplier) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-cashbonsupplierpembayarans')) {
                        $actions .= '<form action="' . route('cashbonsupplierpembayarans.destroy', $cashbonsupplier) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        // return Excel::download(new CashbonSupplierPembayaranExport, 'cashbon_supplier_pembayarans-' . date('Y-m-d') . '.xlsx');
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
            'nominal_bayar' => $request->input('nominal_bayar'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'supplier_id' => ['required', 'integer', 'max:255'],
            'nominal_bayar' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $cashbonSupplier = CashbonSupplierPembayaran::create($store_data);


        return to_route('cashbonsupplierpembayarans.index')->with('status', 'CashbonSupplierPembayaran updated successfully.');
    }

    public function show(int $cashbonSupplier): View
    {

        $data = CashbonSupplierPembayaran::find($cashbonSupplier);
        $data->supplier = $data->supplier->nama;

        // dd($data);


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe
        // dd($cashbonSupplier);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(int $cashbonSupplier): View
    {

        $data = CashbonSupplierPembayaran::find($cashbonSupplier);


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, int $cashbonSupplier): RedirectResponse
    {
        // dd($request->all());
        $cashbonSupplier = CashbonSupplierPembayaran::find($cashbonSupplier);



        // dd("current user id: " . $current_user_id);
        $store_data = [
            'supplier_id' => $request->input('supplier_id'),
            'nominal_bayar' => $request->input('nominal_bayar'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'supplier_id' => ['required', 'integer', 'max:255'],
            'nominal_bayar' => ['required', 'integer'],
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



        return to_route('cashbonsupplierpembayarans.index')->with('status', 'CashbonSupplierPembayaran updated successfully.');
    }

    //soft delete
    public function destroy(int $cashbonSupplier): RedirectResponse
    {
        $cashbonSupplier = CashbonSupplierPembayaran::find($cashbonSupplier);
        $cashbonSupplier->update(['deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('cashbonsupplierpembayarans.index')->with('status', 'CashbonSupplierPembayaran deleted successfully.');
    }
}
