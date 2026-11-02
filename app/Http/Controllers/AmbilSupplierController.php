<?php

namespace App\Http\Controllers;

use App\Models\AmbilSupplier;
use Illuminate\Http\Request;
use App\Models\Supplier;

class AmbilSupplierController extends Controller
{
    public function index()
    {
        return view('ambilsuppliers.index');
    }

    public function indexTable()
    {
        if (request()->ajax()) {
            $data = AmbilSupplier::with('supplier')
                ->select('ambil_suppliers.*');
            return datatables()->of($data)
                ->editColumn('nominal_ambil', function ($ambilSupplier) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($ambilSupplier->nominal_ambil, 0, ',', '.');
                })
                ->addColumn('supplier', function ($row) {
                    return $row->supplier->nama;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })
                ->addColumn('actions', function ($row) {
                    $actions = '';

                    

                    if (auth()->user()->hasPermission('edit-ambil-suppliers')) {
                        $actions .= '<a href="' . route('ambilsuppliers.edit', $row) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-ambil-suppliers')) {
                        $actions .= '<form action="' . route('ambilsuppliers.destroy', $row) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions'])
            
                ->make(true);
        }
    }

    public function create()
    {
        // dd($request->all());
        $supplierOptions = Supplier::all()->map(function($s) {
            return ['value' => $s->id, 'label' => $s->nama];
        });

        
        return view('ambilsuppliers.create', compact('supplierOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'nominal_ambil' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);


        AmbilSupplier::create([
            'supplier_id' => $request->supplier_id,
            'nominal_ambil' => $request->nominal_ambil,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('ambilsuppliers.index')->with('status', 'Ambil supplier created successfully.');
    }

    public function edit(AmbilSupplier $ambilSupplier)
    {
        $supplierOptions = Supplier::all()->map(function($s) {
            return ['value' => $s->id, 'label' => $s->nama];
        });

        return view('ambilsuppliers.edit', compact('supplierOptions', 'ambilSupplier'));
    }

    public function update(Request $request, AmbilSupplier $ambilSupplier)
    {
        $ambilSupplier->update($request->all());
        return redirect()->route('ambilsuppliers.index');
    }

    public function destroy(AmbilSupplier $ambilSupplier)
    {
        $ambilSupplier->delete();
        return redirect()->route('ambilsuppliers.index')->with('status', 'Ambil supplier deleted successfully.');
    }
}
