<?php

namespace App\Http\Controllers;

use App\Models\StokTitipan;
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
            
                ->make(true);
        }
    }
}
