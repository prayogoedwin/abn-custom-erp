<?php

namespace App\Http\Controllers;

use App\Models\StokTitipan;
use App\Models\Supplier;
use App\Models\TitipSupplier;
use Illuminate\Http\Request;

class TitipSupplierController extends Controller
{
    public function index()
    {
        return view('titipsuppliers.index');
    }

    public function indexTable()
    {
        if (request()->ajax()) {
            $data = TitipSupplier::with('supplier')
                ->select('titip_suppliers.*');
            return datatables()->of($data)
                ->editColumn('nominal_titip', function ($titipSupplier) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($titipSupplier->nominal_titip, 0, ',', '.');
                })
                ->addColumn('supplier', function ($row) {
                    return $row->supplier->nama;
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })
                ->addColumn('actions', function ($row) {
                    $actions = '';

                    

                    if (auth()->user()->hasPermission('edit-titip-suppliers')) {
                        $actions .= '<a href="' . route('titipsuppliers.edit', $row) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-titip-suppliers')) {
                        $actions .= '<form action="' . route('titipsuppliers.destroy', $row) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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

        
        return view('titipsuppliers.create', compact('supplierOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'nominal_titip' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);


        TitipSupplier::create([
            'supplier_id' => $request->supplier_id,
            'nominal_titip' => $request->nominal_titip,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('titipsuppliers.index')->with('status', 'Titip supplier created successfully.');
    }

    public function edit(TitipSupplier $titipSupplier)
    {
        $supplierOptions = Supplier::all()->map(function($s) {
            return ['value' => $s->id, 'label' => $s->nama];
        });

        return view('titipsuppliers.edit', compact('supplierOptions', 'titipSupplier'));
    }

    public function update(Request $request, TitipSupplier $titipSupplier)
    {
        $titipSupplier->update($request->all());
        return redirect()->route('titipsuppliers.index');
    }

    public function destroy(TitipSupplier $titipSupplier)
    {
        $titipSupplier->delete();
        return redirect()->route('titipsuppliers.index')->with('status', 'Titip supplier deleted successfully.');
    }
}
