<?php

namespace App\Http\Controllers;

use App\Models\StokTitipan;
use Illuminate\Http\Request;

class StokTitipanController extends Controller
{
    public function index()
    {
        return view('stok-titipan.index');
    }

    // columns: [
    //     { data: 'produk.nama_produk', name: 'produk.nama_produk' },
    //     { data: 'supplier.nama', name: 'supplier.nama' },
    //     { data: 'tipe_stok', name: 'tipe_stok' },
    //     { data: 'jumlah', name: 'jumlah' },
    //     { data: 'keterangan', name: 'keterangan' },
    //     { data: 'tanggal', name: 'tanggal' },

    // ],
    public function indexTable()
    {
        if (request()->ajax()) {
            // Pastikan relasi 'produk' dan 'supplier' sudah terdefinisi di model StokTitipan
            $data = StokTitipan::with(['produk', 'supplier'])->select('stok_titipans.*'); // Sesuaikan dengan nama tabel Anda

            return datatables()->of($data)
                // Fix Fitur Pencarian untuk kolom Relasi Produk
                ->filterColumn('produk.nama_produk', function ($query, $keyword) {
                    $query->whereHas('produk', function ($q) use ($keyword) {
                        $q->where('nama_produk', 'like', "%{$keyword}%");
                    });
                })
                // Fix Fitur Pencarian untuk kolom Relasi Supplier
                ->filterColumn('supplier.nama', function ($query, $keyword) {
                    $query->whereHas('supplier', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d-m-Y'); // Format tanggal sesuai kebutuhan
                })
               
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }
}
