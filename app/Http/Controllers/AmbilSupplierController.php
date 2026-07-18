<?php

namespace App\Http\Controllers;

use App\Models\AmbilSupplier;
use Illuminate\Http\Request;

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
            
                ->make(true);
        }
    }
}
