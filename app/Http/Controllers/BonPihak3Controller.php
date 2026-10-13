<?php

namespace App\Http\Controllers;

use App\Models\Pihak3;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\BonPihak3;


class BonPihak3Controller extends Controller
{
    public function index()
    {
        $pihak3s = Pihak3::all();
        return view('bon-pihak3s.index', compact('pihak3s'));
    }

    public function indexTable(Request $request)
    {
        if ($request->ajax()) {

            if (!$request->supplier) {
                return response()->json(['data' => []]);
            }

            $bonSuppliers = BonPihak3::where('pihak3_id', $request->supplier);

            return DataTables::of($bonSuppliers)
                ->addIndexColumn()
                ->addColumn('actions', function ($bonSupplier) {
                    $actions = '';

                    $actions .= '<a href="' . route('bon-pihak3s.show', $bonSupplier) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';


                    $actions .= '<form action="' . route('bon-pihak3s.delete', $bonSupplier) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
            'supplier_id' => 'required|exists:pihak3s,id',
        ]);
        // dd($request->all());

        $supplier = Pihak3::findOrFail($request->supplier_id);
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


        $currentSisa = BonPihak3::where('pihak3_id', $request->supplier_id)->orderBy('id', 'desc')->first()?->generateSisa() ?? 0;
        $currentSisa = number_format($currentSisa, 0, ',', '.');
        return view('bon-pihak3s.create', compact('currentSisa', 'supplier', 'metodeOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pihak3_id' => 'required|exists:pihak3s,id',
            'metode' => 'required|string',
            'masuk' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'keluar' => 'nullable|numeric|min:0',
        ]);

        $currentSisa = BonPihak3::where('pihak3_id', $request->pihak3_id)->orderBy('id', 'desc')->first()?->generateSisa() ?? 0;

        BonPihak3::create([
            'pihak3_id' => $request->pihak3_id,
            'metode' => $request->metode,
            'masuk' => $request->masuk ?? 0,
            'keluar' => $request->keluar ?? 0,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('bon-pihak3s.index')->with('status', 'Bon pihak3 created successfully.');
    }

    public function show(BonPihak3 $bonSupplier)
    {

        //hitung ulang sisa
        $bonSupplier->sisa = $bonSupplier->generateSisa();
        $bonSupplier->save();
        // dd($bonSupplier);



        return view('bon-pihak3s.show', compact('bonSupplier'));
    }

    public function edit(BonPihak3 $bonSupplier)
    {
        // return view('bon-pihak3s.edit', compact('bonSupplier'));
    }

    public function delete(BonPihak3 $bonSupplier)
    {
        $bonSupplier->delete();
        return redirect()->route('bon-pihak3s.index')->with('status', 'Bon pihak3 deleted successfully.');
    }

}
