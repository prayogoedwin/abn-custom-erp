<?php

namespace App\Http\Controllers;

use App\Exports\CashbonSupplierPembayaranExport;
use App\Models\CashbonPihak3Pembayaran;
use App\Models\Pihak3;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CashbonPihak3PembayaranController extends Controller
{
    private function getPagedata()
    {
        
        $pihak3s = Pihak3::where('deleted_at', null)->get();

        $pagedata = [
            'title' => 'Cashbon Pihak 3 Pembayaran',
            'tablename' => 'cashbonpihak3pembayarans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'pihak3_id', 'value' => 'pihak3',  'title' => 'Pihak 3', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$pihak3s->map(function ($pihak3) {
                        return ['value' => $pihak3->id, 'label' => $pihak3->nama];
                    })->toArray(),
                ]],
                ['name' => 'nominal_bayar', 'value' => 'nominal_bayar', 'title' => 'Nominal Pembayaran', 'type' => 'number', 'inform' => true, 'intable' => true],
                ['name' => 'tipe', 'value' => 'tipe',  'title' => 'Tipe', 'type' => 'select', 'inform' => true, 'intable' => false, 'options' => [
                    ['value' => 'Cash', 'label' => 'Cash'],
                    ['value' => 'Transfer', 'label' => 'Transfer'],

                ]],
                ['name' => 'keterangan', 'value' => 'keterangan', 'title' => 'Keterangan', 'type' => 'text', 'inform' => true, 'intable' => true],

            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        // $cashbon_pihak3_pembayarans = CashbonPihak3Pembayaran::where('cashbon_pihak3pembayarans.deleted_at', null)
        //     ->join('pihak3s', 'cashbon_pihak3pembayarans.pihak3_id', '=', 'pihak3s.id')
        //     ->select('cashbon_pihak3pembayarans.*', 'pihak3s.nama as pihak3')
        //     ->get();
        // dd($cashbon_pihak3_pembayarans);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $cashbon_pihak3_pembayarans = CashbonPihak3Pembayaran::with('pihak3')
                ;
            // dd($cashbon_pihak3_pembayarans);

            return DataTables::of($cashbon_pihak3_pembayarans)
                ->editColumn('nominal_bayar', function ($cashbon_pihak3_pembayaran) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($cashbon_pihak3_pembayaran->nominal_bayar, 0, ',', '.');
                })

                ->addColumn('pihak3', function ($cashbon_pihak3_pembayaran) {
                    return $cashbon_pihak3_pembayaran->pihak3->nama;
                })



                ->addColumn('actions', function ($cashbon_pihak3_pembayaran) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-cashbonpihak3pembayarans')) {
                        $actions .= '<a href="' . route('cashbonpihak3pembayarans.show', $cashbon_pihak3_pembayaran) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-cashbonpihak3pembayarans')) {
                        $actions .= '<a href="' . route('cashbonpihak3pembayarans.edit', $cashbon_pihak3_pembayaran) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-cashbonpihak3pembayarans')) {
                        $actions .= '<form action="' . route('cashbonpihak3pembayarans.destroy', $cashbon_pihak3_pembayaran) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        // TODO:
        // return Excel::download(new CashbonSupplierPembayaranExport, 'cashbon_supplier_pembayarans-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'pihak3_id' => $request->input('pihak3_id'),
            'nominal_bayar' => $request->input('nominal_bayar'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'pihak3_id' => ['required', 'integer', 'max:255'],
            'nominal_bayar' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $cashbonPihak3 = CashbonPihak3Pembayaran::create($store_data);


        return to_route('cashbonpihak3pembayarans.index')->with('status', 'CashbonPihak3Pembayaran updated successfully.');
    }

    public function show(int $cashbonPihak3): View
    {

        $data = CashbonPihak3Pembayaran::find($cashbonPihak3);
        $data->pihak3 = $data->pihak3->nama;

        // dd($data);


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe
        // dd($cashbonPihak3);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(int $cashbonPihak3): View
    {

        $data = CashbonPihak3Pembayaran::find($cashbonPihak3);


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, int $cashbonPihak3): RedirectResponse
    {
        // dd($request->all());
        $cashbonPihak3 = CashbonPihak3Pembayaran::find($cashbonPihak3);



        // dd("current user id: " . $current_user_id);
        $store_data = [
            'pihak3_id' => $request->input('pihak3_id'),
            'nominal_bayar' => $request->input('nominal_bayar'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'pihak3_id' => ['required', 'integer', 'max:255'],
            'nominal_bayar' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $cashbonPihak3->update($store_data);


        // dd("cashbonPihak3 updated: " . json_encode($cashbonPihak3));



        return to_route('cashbonpihak3pembayarans.index')->with('status', 'CashbonPihak3Pembayaran updated successfully.');
    }

    //soft delete
    public function destroy(int $cashbonPihak3): RedirectResponse
    {
        $cashbonPihak3 = CashbonPihak3Pembayaran::find($cashbonPihak3);
        $cashbonPihak3->update(['deleted_by' => auth()->id(),]);
        $cashbonPihak3->delete();


        return to_route('cashbonpihak3pembayarans.index')->with('status', 'CashbonPihak3Pembayaran deleted successfully.');
    }
}
