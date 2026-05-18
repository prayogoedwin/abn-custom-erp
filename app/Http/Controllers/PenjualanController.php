<?php

namespace App\Http\Controllers;

use App\Exports\PenjualanExport;
use App\Models\Customer;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\SimpanPinjamSupplier;
use App\Models\Supplier;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
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

    private function getPagedata()
    {

        // $fillable = [
        //     'no_transaksi_penjualan',
        //     'pengiriman_id',
        //     'customer_id',

        //     'deleted_at',
        //     'created_by',
        //     'updated_by',
        //     'deleted_by',
        // ];

        $pengirimans = Pengiriman::where('deleted_at', null)->get();
        $customers = Customer::where('deleted_at', null)->get();
        // dd($suppliers);

        $pagedata = [
            'title' => 'Penjualan',
            'tablename' => 'penjualans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'no_transaksi_penjualan', 'value' => 'no_transaksi_penjualan',  'title' => 'No Transaksi', 'type' => 'text', 'inform' => false, 'inshow' => true, 'intable' => true],
                ['name' => 'pengiriman_id', 'value' => 'pengiriman',  'title' => 'Pengiriman', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$pengirimans->map(function ($pengiriman) {
                        $label = $pengiriman->no_transaksi . ' - ' . $pengiriman->nopol . ' - ' . $pengiriman->customer->nama;
                        return ['value' => $pengiriman->id, 'label' => $label];
                    })->toArray(),
                ]],

                ['name' => 'customer_id', 'value' => 'customer',  'title' => 'Customer', 'type' => 'select', 'inform' => false, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$customers->map(function ($customer) {
                        return ['value' => $customer->id, 'label' => $customer->nama];
                    })->toArray(),
                ]],


            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        // $penjualans = Penjualan::join('customers', 'penjualans.customer_id', '=', 'customers.id')
        //     ->join('pengirimans', 'penjualans.pengiriman_id', '=', 'pengirimans.id')

        //     // Select everything from pengiriman, and specific fields from users
        //     ->select('penjualans.*', 'customers.nama as customer')
        //     ->where('penjualans.deleted_at', null)
        //     ->get();
        // dd($penjualans);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $penjualans = Penjualan::join('customers', 'penjualans.customer_id', '=', 'customers.id')
                ->join('pengirimans', 'penjualans.pengiriman_id', '=', 'pengirimans.id')
                // Select everything from pengiriman, and specific fields from users
                ->select('penjualans.*', 'customers.nama as customer', 'pengirimans.no_transaksi as pengiriman')
                ->where('penjualans.deleted_at', null)
                ->get();
            // dd($penjualans);

            return DataTables::of($penjualans)




                ->addColumn('actions', function ($pengiriman) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-penjualans')) {
                        $actions .= '<a href="' . route('penjualans.show', $pengiriman) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-penjualans')) {
                        $actions .= '<a href="' . route('penjualans.edit', $pengiriman) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-penjualans')) {
                        $actions .= '<form action="' . route('penjualans.destroy', $pengiriman) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        //TODO:
        // return Excel::download(new PenjualanExport, 'penjualans-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $customers = Customer::where('deleted_at', null)->get();
        $pengirimans = Pengiriman::where('deleted_at', null)->get();
        $pengirimandetails = PengirimanDetail::where('deleted_at', null)->get();
        $produks = Produk::where('deleted_at', null)->get();



        $pagedata = $this->getPagedata();

        return view('penjualans.create', compact('customers', 'pengirimans', 'pengirimandetails', 'produks'), $pagedata);
    }


    public function store(Request $request): RedirectResponse
    {

        // dd($request->all());
        // dd(PengirimanDetail::find(1));


        $store_data = [
            'pengiriman_id' => $request->input('pengiriman_id'),

            'created_by' => auth()->id(),
        ];

        $pengiriman = Pengiriman::find($store_data['pengiriman_id']);
        $store_data['no_transaksi_penjualan'] = $pengiriman->no_transaksi;
        $store_data['customer_id'] = $pengiriman->customer->id;


        $validate = Validator::make($store_data, [
            'pengiriman_id' => ['required', 'integer', 'max:255'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $penjualan = Penjualan::create($store_data);
        if ($request->pengiriman_detail_id != null) {
            foreach ($request->pengiriman_detail_id as $index => $pengiriman_detail_id) {
                if ($pengiriman_detail_id) {

                    $pengirimandetail = PengirimanDetail::find($pengiriman_detail_id);

                    $detail = [
                        'selisih' => $request->selisih[$index],
                        'sub_total' => $request->sub_total[$index],
                        'pph' => $request->pph[$index],
                        'ppn' => $request->ppn[$index],
                        'nominal_akhir' => $request->nominal_akhir[$index],
                    ];

                    if ($request->tipe[$index] == "Titip") {
                        $detail['selisih'] = 0;
                        $detail['sub_total'] = 0;
                        $detail['pph'] = 0;
                        $detail['ppn'] = 0;
                        $detail['nominal_akhir'] = 0;
                    }

                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'pengiriman_detail_id' => $request->pengiriman_detail_id[$index],
                        'produk_id' => $request->produk_id[$index],
                        'tipe'    => $request->tipe[$index],
                        'netto_pengiriman'    => $pengirimandetail->netto,
                        'netto'    => $request->netto[$index],
                        'selisih'    => $detail['selisih'],
                        'basis_harga'    => $request->basis_harga[$index],
                        'sub_total'    => $detail['sub_total'],
                        'pph'    => $detail['pph'] ? $detail['pph'] : 0,
                        'ppn'    => $detail['ppn'],
                        'nominal_akhir'    => $detail['nominal_akhir'],

                        'created_by' => $store_data['created_by'],
                    ]);
                }
            }
        }





        return to_route('penjualans.index')->with('status', "penjualans created succesfully");
    }


    public function show(int $id): View
    {
        $penjualan = Penjualan::find($id);
        // dd($penjualan);

        // dd($penjualan->details);
        // dd(PenjualanDetail::get());


        $pagedata = $this->getPagedata();



        return view('penjualans.show', compact('penjualan'), $pagedata);
    }

    public function edit(Penjualan $penjualan): View
    {

        $data = $penjualan;

        $customers = Customer::where('deleted_at', null)->get();
        $pengirimans = Pengiriman::where('deleted_at', null)->get();
        $pengirimandetails = PengirimanDetail::where('deleted_at', null)->get();
        $produks = Produk::where('deleted_at', null)->get();

        // dd($data);

        $pagedata = $this->getPagedata();

        return view('penjualans.edit', compact('data', 'customers', 'pengirimans', 'pengirimandetails', 'produks'), $pagedata);
    }

    public function update(Request $request, Penjualan $penjualan): RedirectResponse
    {
        $store_data = [
            'pengiriman_id' => $request->input('pengiriman_id'),

            'created_by' => auth()->id(),
        ];

        $pengiriman = Pengiriman::find($store_data['pengiriman_id']);
        $store_data['no_transaksi_penjualan'] = $pengiriman->no_transaksi;
        $store_data['customer_id'] = $pengiriman->customer->id;


        $validate = Validator::make($store_data, [
            'pengiriman_id' => ['required', 'integer', 'max:255'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $penjualan->update($store_data);
        // Hapus semua dulu
        PenjualanDetail::where('penjualan_id', $penjualan->id)->delete();

        if ($request->pengiriman_detail_id != null) {
            foreach ($request->pengiriman_detail_id as $index => $pengiriman_detail_id) {
                if ($pengiriman_detail_id) {

                    $pengirimandetail = PengirimanDetail::find($pengiriman_detail_id);

                    $detail = [
                        'selisih' => $request->selisih[$index],
                        'sub_total' => $request->sub_total[$index],
                        'pph' => $request->pph[$index],
                        'ppn' => $request->ppn[$index],
                        'nominal_akhir' => $request->nominal_akhir[$index],
                    ];

                    if ($request->tipe[$index] == "Titip") {
                        $detail['selisih'] = 0;
                        $detail['sub_total'] = 0;
                        $detail['pph'] = 0;
                        $detail['ppn'] = 0;
                        $detail['nominal_akhir'] = 0;
                    }

                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'pengiriman_detail_id' => $request->pengiriman_detail_id[$index],
                        'produk_id' => $request->produk_id[$index],
                        'tipe'    => $request->tipe[$index],
                        'netto_pengiriman'    => $pengirimandetail->netto,
                        'netto'    => $request->netto[$index],
                        'selisih'    => $detail['selisih'],
                        'basis_harga'    => $request->basis_harga[$index],
                        'sub_total'    => $detail['sub_total'],
                        'pph'    => $detail['pph'] ? $detail['pph'] : 0,
                        'ppn'    => $detail['ppn'],
                        'nominal_akhir'    => $detail['nominal_akhir'],

                        'created_by' => $store_data['created_by'],
                    ]);
                }
            }
        }




        return to_route('penjualans.index')->with('status', 'Penjualan updated successfully.');
    }

    //soft delete
    public function destroy(Penjualan $penjualan): RedirectResponse
    {

        $penjualan->update(['deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('penjualans.index')->with('status', 'Penjualan deleted successfully.');
    }
}
