<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BonSupplier;
use App\Models\Supplier;
use Yajra\DataTables\Facades\DataTables;

class BonSupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('bon-suppliers.index', compact('suppliers'));
    }

    public function indexTable(Request $request)
    {
        if ($request->ajax()) {

            if (!$request->supplier) {
                return response()->json(['data' => []]);
            }

            $bonSuppliers = BonSupplier::where('supplier_id', $request->supplier);

            return DataTables::of($bonSuppliers)
                ->addIndexColumn()
                ->addColumn('actions', function ($bonSupplier) {
                    $actions = '';

                    


                    $actions .= '<form action="' . route('bon-suppliers.delete', $bonSupplier) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';


                    return $actions;
                })
                ->editColumn('created_at', function ($bonSupplier) {
                    return $bonSupplier->created_at->translatedFormat('d M Y');
                })
                ->editColumn('sisa', function ($bonSupplier) {
                    return $bonSupplier->generateSisa();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
        ]);
        // dd($request->all());

        $supplier = Supplier::findOrFail($request->supplier_id);
        $metodeOptions = [
            [
                'value' => 'cash',
                'label' => 'Cash',
            ],
            [
                'value' => 'transfer',
                'label' => 'Transfer',
            ]
        ];


        $currentSisa = BonSupplier::where('supplier_id', $request->supplier_id)->orderBy('id', 'desc')->first()?->generateSisa() ?? 0;
        $currentSisa = number_format($currentSisa, 0, ',', '.');
        return view('bon-suppliers.create', compact('currentSisa', 'supplier', 'metodeOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'metode' => 'required|string',
            'masuk' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'keluar' => 'nullable|numeric|min:0',
        ]);

        $currentSisa = BonSupplier::where('supplier_id', $request->supplier_id)->orderBy('id', 'desc')->first()?->sisa ?? 0;

        BonSupplier::create([
            'supplier_id' => $request->supplier_id,
            'metode' => $request->metode,
            'masuk' => $request->masuk ?? 0,
            'keluar' => $request->keluar ?? 0,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('bon-suppliers.index')->with('status', 'Bon supplier created successfully.');
    }

    public function show(BonSupplier $bonSupplier)
    {

        //hitung ulang sisa
        $bonSupplier->sisa = $bonSupplier->generateSisa();
        $bonSupplier->save();
        // dd($bonSupplier);



        return view('bon-suppliers.show', compact('bonSupplier'));
    }

    public function edit(BonSupplier $bonSupplier)
    {
        // return view('bon-suppliers.edit', compact('bonSupplier'));
    }

    public function delete(BonSupplier $bonSupplier)
    {
        $bonSupplier->delete();
        return redirect()->route('bon-suppliers.index')->with('status', 'Bon supplier deleted successfully.');
    }
}
