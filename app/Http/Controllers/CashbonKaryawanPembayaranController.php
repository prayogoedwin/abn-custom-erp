<?php

namespace App\Http\Controllers;

use App\Exports\CashbonKaryawanPembayaranExport;
use App\Models\CashbonKaryawanPembayaran;
use App\Models\Karyawan;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CashbonKaryawanPembayaranController extends Controller
{
    private function getPagedata()
    {
        // protected $fillable = [
        //     'karyawan_id',
        //     'nominal_cashbon',
        //     'tipe',
        //     'keterangan',

        //     'created_by',
        //     'updated_by',
        //     'deleted_at',
        //     'deleted_by',
        // ];

        $karyawans = Karyawan::where('deleted_at', null)->get();

        $pagedata = [
            'title' => 'Cashbon Karyawan Pembayaran',
            'tablename' => 'cashbonkaryawanpembayarans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'karyawan_id', 'value' => 'karyawan',  'title' => 'Karyawan', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$karyawans->map(function ($karyawan) {
                        return ['value' => $karyawan->id, 'label' => $karyawan->nama];
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
        // $cashbon_karyawan_pembayarans = CashbonKaryawanPembayaran::where('cashbon_karyawan_pembayarans.deleted_at', null)
        //     ->join('karyawans', 'cashbon_karyawan_pembayarans.karyawan_id', '=', 'karyawans.id')
        //     ->select('cashbon_karyawan_pembayarans.*', 'karyawans.nama as karyawan')
        //     ->get();
        // dd($cashbon_karyawan_pembayarans);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $cashbon_karyawan_pembayarans = CashbonKaryawanPembayaran::where('cashbon_karyawan_pembayarans.deleted_at', null)
                ->join('karyawans', 'cashbon_karyawan_pembayarans.karyawan_id', '=', 'karyawans.id')
                ->select('cashbon_karyawan_pembayarans.*', 'karyawans.nama as karyawan')
                ->get();
            // dd($cashbon_karyawan_pembayarans);

            return DataTables::of($cashbon_karyawan_pembayarans)
                ->editColumn('nominal_bayar', function ($cashbonkaryawan) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($cashbonkaryawan->nominal_bayar, 0, ',', '.');
                })



                ->addColumn('actions', function ($cashbonkaryawan) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-cashbonkaryawanpembayarans')) {
                        $actions .= '<a href="' . route('cashbonkaryawanpembayarans.show', $cashbonkaryawan) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-cashbonkaryawanpembayarans')) {
                        $actions .= '<a href="' . route('cashbonkaryawanpembayarans.edit', $cashbonkaryawan) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-cashbonkaryawanpembayarans')) {
                        $actions .= '<form action="' . route('cashbonkaryawanpembayarans.destroy', $cashbonkaryawan) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        // return Excel::download(new CashbonKaryawanPembayaranExport, 'cashbon_karyawan_pembayarans-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'karyawan_id' => $request->input('karyawan_id'),
            'nominal_bayar' => $request->input('nominal_bayar'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'karyawan_id' => ['required', 'integer', 'max:255'],
            'nominal_bayar' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $cashbonKaryawan = CashbonKaryawanPembayaran::create($store_data);


        return to_route('cashbonkaryawanpembayarans.index')->with('status', 'CashbonKaryawanPembayaran updated successfully.');
    }

    public function show(int $cashbonKaryawan): View
    {

        $data = CashbonKaryawanPembayaran::find($cashbonKaryawan);
        $data->karyawan = $data->karyawan->nama;

        // dd($data);


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe
        // dd($cashbonKaryawan);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(int $cashbonKaryawan): View
    {

        $data = CashbonKaryawanPembayaran::find($cashbonKaryawan);


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, int $cashbonKaryawan): RedirectResponse
    {
        // dd($request->all());
        $cashbonKaryawan = CashbonKaryawanPembayaran::find($cashbonKaryawan);



        // dd("current user id: " . $current_user_id);
        $store_data = [
            'karyawan_id' => $request->input('karyawan_id'),
            'nominal_bayar' => $request->input('nominal_bayar'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'karyawan_id' => ['required', 'integer', 'max:255'],
            'nominal_bayar' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $cashbonKaryawan->update($store_data);


        // dd("cashbonKaryawan updated: " . json_encode($cashbonKaryawan));



        return to_route('cashbonkaryawanpembayarans.index')->with('status', 'CashbonKaryawanPembayaran updated successfully.');
    }

    //soft delete
    public function destroy(int $cashbonKaryawan): RedirectResponse
    {
        $cashbonKaryawan = CashbonKaryawanPembayaran::find($cashbonKaryawan);
        $cashbonKaryawan->update(['deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('cashbonkaryawanpembayarans.index')->with('status', 'CashbonKaryawanPembayaran deleted successfully.');
    }
}
