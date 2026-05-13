<?php

namespace App\Http\Controllers;

use App\Exports\PenjualanExport;
use App\Models\Customer;
use App\Models\Pengiriman;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
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

                ['name' => 'customer_id', 'value' => 'customer',  'title' => 'Customer', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$customers->map(function ($customer) {
                        return ['value' => $customer->id, 'label' => $customer->nama];
                    })->toArray(),
                ]],
                ['name' => 'nopol', 'value' => 'nopol',  'title' => 'Nopol', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],

            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        // $penjualans = Penjualan::join('customers', 'penjualans.customer_id', '=', 'customers.id')
        //         // Select everything from pengiriman, and specific fields from users
        //         ->select('penjualans.*', 'customers.nama as customer')
        //         ->where('penjualans.deleted_at', null)
        //         ->get();
        // dd($penjualans);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $penjualans = Penjualan::join('customers', 'penjualans.customer_id', '=', 'customers.id')
                ->join('pengirimans', 'penjualans.customer_id', '=', 'pengirimans.id')
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
        $pengiriman = Pengiriman::where('deleted_at', null)->get();

        $pagedata = $this->getPagedata();

        return view('penjualans.create', compact('customers', 'pengiriman'), $pagedata);
    }


    public function store(Request $request): RedirectResponse
    {

        $store_data = [
            'nopol' => $request->input('nopol'),
            'customer_id' => $request->input('customer_id'),

            'created_by' => auth()->id(),
        ];

        //$store_data['no_transaksi'] = terjadi di model



        $validate = Validator::make($store_data, [
            'nopol' => ['required', 'string', 'max:255'],
            'customer_id' => ['required', 'integer', 'max:255'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        $pengiriman = Penjualan::create($store_data);



        return to_route('pengirimandetails.create', $pengiriman->id);
    }


    public function show(int $id): View
    {
        $pengiriman = Penjualan::find($id);
        $pengiriman->details = PenjualanDetail::where('pengiriman_id', $id)->get();
        $pengiriman->supplier = Supplier::find($pengiriman->customer_id);

        $pagedata = $this->getPagedata();



        return view('penjualans.show', compact('pengiriman'), $pagedata);
    }

    public function edit(Penjualan $pengiriman): View
    {

        $data = $pengiriman;


        $pagedata = $this->getPagedata();

        return view('penjualans.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Penjualan $pengiriman): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nopol' => $request->input('nopol'),
            'customer_id' => $request->input('customer_id'),

            'updated_by' => auth()->id(),
        ];

        //$store_data['no_transaksi'] = terjadi di model



        $validate = Validator::make($store_data, [
            'nopol' => ['required', 'string', 'max:255'],
            'customer_id' => ['required', 'integer', 'max:255'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $pengiriman->update($store_data);




        return to_route('pengirimandetails.edit', $pengiriman->id);
    }

    //soft delete
    public function destroy(Penjualan $pengiriman): RedirectResponse
    {
        $pengiriman->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('penjualans.index')->with('status', 'Penjualan deleted successfully.');
    }
}
