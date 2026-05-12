<?php

namespace App\Http\Controllers;

use App\Exports\PengirimanExport;
use App\Models\Customer;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
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

class PengirimanController extends Controller
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

        // 'customer_id',
        // 'nopol',
        // 'no_transaksi',
        // 'created_by',
        // 'updated_by',
        // 'deleted_at',
        // 'deleted_by',


        $customers = Customer::where('deleted_at', null)->get();
        // dd($suppliers);

        $pagedata = [
            'title' => 'Pengiriman',
            'tablename' => 'pengirimans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'no_transaksi', 'value' => 'no_transaksi',  'title' => 'No Transaksi', 'type' => 'text', 'inform' => false, 'inshow' => true, 'intable' => true],
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
        // $pengirimans = Pengiriman::join('customers', 'pengirimans.customer_id', '=', 'customers.id')
        //         // Select everything from pengiriman, and specific fields from users
        //         ->select('pengirimans.*', 'customers.nama as customer')
        //         ->where('pengirimans.deleted_at', null)
        //         ->get();
        // dd($pengirimans);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $pengirimans = Pengiriman::join('customers', 'pengirimans.customer_id', '=', 'customers.id')
                // Select everything from pengiriman, and specific fields from users
                ->select('pengirimans.*', 'customers.nama as customer')
                ->where('pengirimans.deleted_at', null)
                ->get();
            // dd($pengirimans);

            return DataTables::of($pengirimans)




                ->addColumn('actions', function ($pengiriman) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-pengirimans')) {
                        $actions .= '<a href="' . route('pengirimans.show', $pengiriman) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-pengirimans')) {
                        $actions .= '<a href="' . route('pengirimans.edit', $pengiriman) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-pengirimans')) {
                        $actions .= '<form action="' . route('pengirimans.destroy', $pengiriman) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new PengirimanExport, 'pengirimans-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $customers = Customer::where('deleted_at', null)->get();

        $pagedata = $this->getPagedata();

        return view('pengirimans.create', compact('customers'), $pagedata);
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


        $pengiriman = Pengiriman::create($store_data);



        return to_route('pengirimandetails.create', $pengiriman->id);
    }


    public function show(int $id): View
    {
        $pengiriman = Pengiriman::find($id);
        $pengiriman->details = PengirimanDetail::where('pengiriman_id', $id)->get();
        $pengiriman->supplier = Supplier::find($pengiriman->customer_id);

        $pagedata = $this->getPagedata();



        return view('pengirimans.show', compact('pengiriman'), $pagedata);
    }

    public function edit(Pengiriman $pengiriman): View
    {

        $data = $pengiriman;


        $pagedata = $this->getPagedata();

        return view('pengirimans.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Pengiriman $pengiriman): RedirectResponse
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
    public function destroy(Pengiriman $pengiriman): RedirectResponse
    {
        $pengiriman->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('pengirimans.index')->with('status', 'Pengiriman deleted successfully.');
    }
}
