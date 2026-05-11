<?php

namespace App\Http\Controllers;

use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
use App\Models\Produk;
use Illuminate\Http\Request;

class PengirimanDetailController extends Controller
{

    private function getPagedata()
    {
        


        $pagedata = [
            'title' => 'PengirimanDetail',
            'tablename' => 'pengirimandetails',
            'tableaction' => true,
            
        ];

        // dd($pagedata);

        return $pagedata;
    }


    public function create($pengiriman_id)
    {

        $produks = Produk::where('deleted_at', null)->get();

        $pagedata = $this->getPagedata();

        return view('pengirimandetails.create', compact('produks', 'pengiriman_id'), $pagedata);
    }

    public function store(Request $request)
    {
        $store_data = [
            'pengiriman_id' => $request->input('pengiriman_id'),
            'nama_produk' => $request->input('nama_produk'),
            'jumlah_per_karung' => $request->input('jumlah_per_karung'),
            'jumlah_karung' => $request->input('jumlah_karung'),
            'bruto' => $request->input('bruto'),
            'tara' => $request->input('tara'),
            'netto' => $request->input('netto'),


            'created_by' => auth()->id(),
        ];

        // dd($request->all());    

        foreach ($request->nama_produk as $index => $nama_produk) {
            if ($nama_produk) {


                PengirimanDetail::create([
                    'pengiriman_id' => $store_data['pengiriman_id'],
                    'nama_barang'    => $request->nama_produk[$index],
                    'jumlah_per_karung'    => $request->jumlah_per_karung[$index],
                    'jumlah_karung'    => $request->jumlah_karung[$index],
                    'bruto'    =>$request->bruto[$index],
                    'tara'    => $request->tara[$index],
                    'netto'    => $request->netto[$index]


                ]);
            }
        }
        return to_route('pengirimans.index')->with('status', "sukses tambah data pengiriman");
    }

    public function edit(Pengiriman $pengiriman)
    {
        $pengirimandetails = $pengiriman->detail()->get();

        // dd($pengirimandetails);
        $data = $pengiriman;
        

        $produks = Produk::where('deleted_at', null)->get();

        $pagedata = $this->getPagedata();

        return view('pengirimandetails.edit', compact('produks', 'pengirimandetails', 'data'), $pagedata);
    }
}
