<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\BonKaryawan;
use Yajra\DataTables\Facades\DataTables;

class BonKaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::whereNull('deleted_at')->get();
        return view('bon-karyawans.index', compact('karyawans'));
    }

    public function indexTable(Request $request)
    {
        if ($request->ajax()) {

            if (!$request->karyawan) {
                return response()->json(['data' => []]);
            }

            $bonSuppliers = BonKaryawan::where('karyawan_id', $request->karyawan);

            return DataTables::of($bonSuppliers)
                ->addIndexColumn()
                ->addColumn('actions', function ($bonSupplier) {
                    $actions = '';



                    $actions .= '<form action="' . route('bon-karyawans.delete', $bonSupplier) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
            'karyawan_id' => 'required|exists:karyawans,id',
        ]);
        // dd($request->all());

        $karyawan = Karyawan::findOrFail($request->karyawan_id);
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


        $currentSisa = BonKaryawan::where('karyawan_id', $request->karyawan_id)->orderBy('id', 'desc')->first()?->generateSisa() ?? 0;
        $currentSisa = number_format($currentSisa, 0, ',', '.');
        return view('bon-karyawans.create', compact('currentSisa', 'karyawan', 'metodeOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'metode' => 'required|string',
            'masuk' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'keluar' => 'nullable|numeric|min:0',
        ]);

        $currentSisa = BonKaryawan::where('karyawan_id', $request->karyawan_id)->orderBy('id', 'desc')->first()?->sisa ?? 0;

        BonKaryawan::create([
            'karyawan_id' => $request->karyawan_id,
            'metode' => $request->metode,
            'masuk' => $request->masuk ?? 0,
            'keluar' => $request->keluar ?? 0,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('bon-karyawans.index')->with('status', 'Bon karyawan created successfully.');
    }

    public function show(BonKaryawan $bonKaryawan)
    {

        //hitung ulang sisa
        $bonKaryawan->sisa = $bonKaryawan->generateSisa();
        $bonKaryawan->save();
        // dd($bonSupplier);



        return view('bon-karyawans.show', compact('bonKaryawan'));
    }

    public function edit(BonKaryawan $bonKaryawan)
    {
        // return view('bon-karyawans.edit', compact('bonKaryawan'));
    }

    public function delete(BonKaryawan $bonKaryawan)
    {
        $bonKaryawan->delete();
        return redirect()->route('bon-karyawans.index')->with('status', 'Bon karyawan deleted successfully.');
    }

}
