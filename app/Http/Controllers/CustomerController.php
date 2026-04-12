<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    private function getPagedata()
    {
        
        $pagedata = [
            'title' => 'Customer',
            'tablename' => 'customers',

            'columns' => [
                ['name' => 'nama', 'value' => 'nama',  'title' => 'Nama Customer', 'type' => 'text', 'inform' => true, 'intable' => true],
                
                ['name' => 'kontak', 'value' => 'kontak', 'title' => 'Kontak', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'alamat', 'value' => 'alamat', 'title' => 'Alamat', 'type' => 'text', 'inform' => true, 'intable' => true],
                
            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        if ($request->ajax()) {
            // dd('masuk ajax');
            $customers = Customer::where('customers.isactive', true)
                ->get();
            // dd($customers);

            return DataTables::of($customers)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('customers.nama_customer', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_customers.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($customer) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-customers')) {
                        $actions .= '<a href="' . route('customers.show', $customer) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-customers')) {
                        $actions .= '<a href="' . route('customers.edit', $customer) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-customers')) {
                        $actions .= '<form action="' . route('customers.destroy', $customer) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new CustomerExport, 'customers-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', compact('kategoris'), $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'nama' => $request->input('nama'),
            'kontak' => $request->input('kontak'),
            'alamat' => $request->input('alamat'),
            
            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama' => ['required', 'string', 'max:255'],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],
            
            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $customer = Customer::create($store_data);
        // // log stok change
        // Stok::create([
        //     'customer_id' => $customer->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'customer_id' => $customer->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('customers.index')->with('status', 'Customer updated successfully.');
    }

    public function show(Customer $customer): View
    {
        
        $data = $customer;


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe



        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Customer $customer): View
    {
       

        $data = $customer;

        

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nama' => $request->input('nama'),
            'kontak' => $request->input('kontak'),
            'alamat' => $request->input('alamat'),
            
            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama' => ['required', 'string', 'max:255'],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],
            
            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $customer->update($store_data);


        // dd("customer updated: " . json_encode($customer));



        return to_route('customers.index')->with('status', 'Customer updated successfully.');
    }

    //soft delete
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('customers.index')->with('status', 'Customer deleted successfully.');
    }
}


