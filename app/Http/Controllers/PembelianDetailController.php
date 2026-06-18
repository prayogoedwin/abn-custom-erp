<?php

namespace App\Http\Controllers;

use App\Exports\PembelianDetailExport;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\SimpanPinjamSupplier;
use App\Models\Stok;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PembelianDetailController extends Controller
{
    private function toIntMoney($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        $cleaned = preg_replace('/[^\d\-]/', '', (string) $value);
        return $cleaned === '' || $cleaned === '-' ? 0 : (int) $cleaned;
    }

    private function syncPembelianTotals(int $pembelianId): void
    {
        $pembelian = Pembelian::find($pembelianId);
        if (!$pembelian) {
            return;
        }

        $totalNominalPembelian = (int) PembelianDetail::where('pembelian_id', $pembelianId)->sum('harga_netto');
        $totalNominalTerbayar = (int) SimpanPinjamSupplier::where('pembelian_id', $pembelianId)->sum('nominal');
        $kekurangan = max($totalNominalPembelian - $totalNominalTerbayar, 0);
        $statusPembayaran = $kekurangan <= 0 ? 'Lunas' : 'Belum Lunas';

        $pembelian->update([
            'total_nominal_pembelian' => $totalNominalPembelian,
            'total_nominal_terbayar' => $totalNominalTerbayar,
            'kekurangan' => $kekurangan,
            'status_pembayaran' => $statusPembayaran,
        ]);
    }

    private function getPagedata()
    {
        //     'pembelian_id',
        //     'produk_id',
        //     'netto',
        //     'satuan',
        //     'rendeman',
        //     'bobot',
        //     'harga',
        //     'harga_basis',
        //     'harga_basis_pembelian',
        //     'harga_netto',

        $produks = Produk::where('isActive', true)->get();

        $pagedata = [
            'title' => 'PembelianDetail',
            'tablename' => 'pembeliandetails',
            'tableaction' => true,
            'columns' => [
                ['name' => 'pembelian_id', 'value' => 'pembelian_id',  'title' => 'pembelian_id', 'type' => 'text', 'inform' => false, 'inshow' => true, 'intable' => false],
                ['name' => 'produk_id', 'value' => 'produk', 'title' => 'Produk', 'type' => 'select', 'inform' => true, 'inshow' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$produks->map(function ($produk) {
                        return ['value' => $produk->id, 'label' => $produk->nama_produk];
                    })->toArray(),
                ]],
                ['name' => 'rendeman', 'value' => 'rendeman', 'title' => 'Rendeman', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'bobot', 'value' => 'bobot', 'title' => 'bobot', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'netto', 'value' => 'netto', 'title' => 'Netto', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'satuan', 'value' => 'satuan', 'title' => 'satuan', 'type' => 'string', 'inform' => true, 'inshow' => true, 'intable' => true],

            ],
        ];

        // dd($pagedata);

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        if ($request->ajax()) {
            // dd('masuk ajax');
            $pembeliandetails = PembelianDetail::where('pembeliandetails.isactive', true)
                ->get();
            // dd($pembeliandetails);

            return DataTables::of($pembeliandetails)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('pembeliandetails.nama_pembelian', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_pembeliandetails.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($pembelian) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pembeliandetails')) {
                        $actions .= '<a href="' . route('pembeliandetails.show', $pembelian) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pembeliandetails')) {
                        $actions .= '<a href="' . route('pembeliandetails.edit', $pembelian) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pembeliandetails')) {
                        $actions .= '<form action="' . route('pembeliandetails.destroy', $pembelian) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new PembelianDetailExport, 'pembeliandetails-' . date('Y-m-d') . '.xlsx');
    }

    public function createnow(Pembelian $pembelian):View
    {
        $produks = Produk::where('isActive', true)->get();



        $pagedata = $this->getPagedata();
        $pagedata['pembelian_id'] = $pembelian->id;

        return view('pembeliandetails.createnow', compact('produks'), $pagedata);
    }


    public function store(Request $request): RedirectResponse
    {
       
        //      'pembelian_id',
        //     'produk_id',
        //     'tipe_transaksi_pembelian'
        //     'netto',
        //     'satuan',
        //     'rendeman',
        //     'bobot',
        //     'harga',
        //     'harga_basis',
        //     'harga_basis_pembelian',
        //     'harga_netto',



        foreach ($request->produk_id as $index => $produk_id) {
            if ($produk_id) {

                
                PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'tipe_transaksi_pembelian'         => $request->tipe_pembelian[$index],
                    'netto'        => $request->netto[$index],
                    //TODO:
                    'satuan'       => 'kg', //sementara hardcode
                    'rendeman'     => $request->rendeman[$index] ?? null,
                    'bobot'     => $this->toIntMoney($request->bobot[$index] ?? 0),
                    'harga'     => $this->toIntMoney($request->harga[$index] ?? 0),
                    'harga_basis'     => $this->toIntMoney($request->harga_basis[$index] ?? 0),
                    'harga_basis_pembelian'     => $this->toIntMoney($request->harga_basis_pembelian[$index] ?? ($request->harga_basis[$index] ?? 0)),
                    'harga_netto'     => $this->toIntMoney($request->harga_netto[$index] ?? 0),
                ]);
            }
        }

        $this->syncPembelianTotals((int) $request->pembelian_id);




        return to_route('pembelians.createlanjut', $request->pembelian_id);
    }

    public function show(PembelianDetail $pembelian): View
    {

        $data = $pembelian;


        $pagedata = $this->getPagedata();

       

        return view('dynamiccrud.show', compact('data'), $pagedata);
    }


    public function editnow(Pembelian $pembelian): View
    {
        
        $produks = Produk::all();

        $pagedata = $this->getPagedata();

        // dd($pembeliandetails);

        return view('pembeliandetails.editnow', compact('pembelian', 'produks'), $pagedata);
    }

    public function titipupdate(Request $request): RedirectResponse 
    {

        $store_data = [
            'pembelian_id' => $request->input('pembelian_id'),
            'produk_id' => $request->input('produk_id'),
            'netto' => $request->input('netto'),
            'satuan' => $request->input('satuan'),
            'rendeman' => $request->input('rendeman'),
            'bobot' => $request->input('bobot'),
            'harga' => $request->input('harga'),
            'harga_basis' => $request->input('harga_basis'),
            'harga_basis_pembelian' => $request->input('harga_basis_pembelian'),
            'harga_netto' => $request->input('harga_netto'),

            'created_by' => auth()->id(),
        ];

        // dd($request->all());

        // Hapus semua dulu
        PembelianDetail::where('pembelian_id', $request->pembelian_id)->delete();


        foreach ($request->produk_id as $index => $produk_id) {
            if (!empty($produk_id)) {
                // Simpan ulang setiap item ke database
                PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'netto'        => $request->netto[$index] ?? 0,
                    'satuan'       => $request->satuan[$index] ?? "satuan",
                    'rendeman'     => $request->rendeman[$index] ?? null,

                    'updated_by' => auth()->id(),
                ]);
            }
        }

        $this->syncPembelianTotals((int) $store_data['pembelian_id']);


        return to_route('pembelians.createlanjut', $store_data['pembelian_id']);

    }

    public function jualupdate(Request $request): RedirectResponse 
    {

        $store_data = [
            'pembelian_id' => $request->input('pembelian_id'),
            'produk_id' => $request->input('produk_id'),
            'netto' => $request->input('netto'),
            'satuan' => $request->input('satuan'),
            'rendeman' => $request->input('rendeman'),
            'bobot' => $request->input('bobot'),
            'harga' => $request->input('harga'),
            'harga_basis' => $request->input('harga_basis'),
            'harga_basis_pembelian' => $request->input('harga_basis_pembelian'),
            'harga_netto' => $request->input('harga_netto'),

            'created_by' => auth()->id(),
        ];

        // dd($request->all());

        // Hapus semua dulu
        PembelianDetail::where('pembelian_id', $request->pembelian_id)->delete();


        foreach ($request->produk_id as $index => $produk_id) {
            if (!empty($produk_id)) {
                // Simpan ulang setiap item ke database
                PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'netto'        => $request->netto[$index] ?? 0,
                    'satuan'       => $request->satuan[$index] ?? "satuan",
                    'rendeman'     => $request->rendeman[$index] ?? null,
                    'bobot'     => $this->toIntMoney($request->bobot[$index] ?? 0),
                    'harga'     => $this->toIntMoney($request->harga[$index] ?? 0),
                    'harga_basis'     => $this->toIntMoney($request->harga_basis[$index] ?? 0),
                    'harga_basis_pembelian'     => $this->toIntMoney($request->harga_basis_pembelian[$index] ?? ($request->harga_basis[$index] ?? 0)),
                    'harga_netto'     => $this->toIntMoney($request->harga_netto[$index] ?? 0),

                    'updated_by' => auth()->id(),
                ]);
            }
        }

        $this->syncPembelianTotals((int) $store_data['pembelian_id']);


        return to_route('pembelians.createlanjut', $store_data['pembelian_id']);

    }



    //soft delete
    public function destroy(PembelianDetail $pembelian): RedirectResponse
    {
        $pembelian->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pembeliandetails.index')->with('status', 'PembelianDetail deleted successfully.');
    }
}
