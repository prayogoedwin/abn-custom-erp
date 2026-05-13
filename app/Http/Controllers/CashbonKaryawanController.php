<?php

namespace App\Http\Controllers;

use App\Exports\CashbonKaryawanExport;
use App\Models\CashbonKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CashbonKaryawanController extends Controller
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
            'title' => 'Cashbon Karyawan',
            'tablename' => 'cashbonkaryawans',
            'tableaction' => true,
            'columns' => [
                ['name' => 'karyawan_id', 'value' => 'karyawan',  'title' => 'Karyawan', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$karyawans->map(function ($karyawan) {
                        return ['value' => $karyawan->id, 'label' => $karyawan->nama];
                    })->toArray(),
                ]],
                ['name' => 'nominal_cashbon', 'value' => 'nominal_cashbon', 'title' => 'Nominal Cashbon', 'type' => 'number', 'inform' => true, 'intable' => true],
                ['name' => 'tipe', 'value' => 'tipe',  'title' => 'Tipe', 'type' => 'select', 'inform' => true, 'intable' => false, 'options' => [
                    ['value' => 'Cash', 'label' => 'Cash'],
                    ['value' => 'Transfer', 'label' => 'Transfer'],

                ]],

            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        // dd($request->headers->all());
        // $cashbon_karyawans = CashbonKaryawan::join('karyawans', 'cashbon_karyawans.karyawan_id', '=', 'karyawans.id')
        //     ->select('cashbon_karyawans.*', 'karyawans.nama as karyawan')
        //     ->get();
        // dd($cashbon_karyawans);
        if ($request->ajax()) {
            // dd('masuk ajax');
            $cashbon_karyawans = CashbonKaryawan::where('cashbon_karyawans.deleted_at', null)
                ->join('karyawans', 'cashbon_karyawans.karyawan_id', '=', 'karyawans.id')
                ->select('cashbon_karyawans.*', 'karyawans.nama as karyawan')
                ->get();
            // dd($cashbon_karyawans);

            return DataTables::of($cashbon_karyawans)




                ->addColumn('actions', function ($cashbonkaryawan) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-cashbonkaryawans')) {
                        $actions .= '<a href="' . route('cashbonkaryawans.show', $cashbonkaryawan) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-cashbonkaryawans')) {
                        $actions .= '<a href="' . route('cashbonkaryawans.edit', $cashbonkaryawan) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-cashbonkaryawans')) {
                        $actions .= '<form action="' . route('cashbonkaryawans.destroy', $cashbonkaryawan) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        // return Excel::download(new CashbonKaryawanExport, 'cashbon_karyawans-' . date('Y-m-d') . '.xlsx');
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
            'nominal_cashbon' => $request->input('nominal_cashbon'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'karyawan_id' => ['required', 'integer', 'max:255'],
            'nominal_cashbon' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }



        $cashbonKaryawan = CashbonKaryawan::create($store_data);


        return to_route('cashbonkaryawans.index')->with('status', 'CashbonKaryawan updated successfully.');
    }

    public function show(int $cashbonKaryawan): View
    {

        $data = CashbonKaryawan::find($cashbonKaryawan);
        $data->karyawan = $data->karyawan->nama;

        // dd($data);


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe
        // dd($cashbonKaryawan);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(int $cashbonKaryawan): View
    {

        $data = CashbonKaryawan::find($cashbonKaryawan);


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, int $cashbonKaryawan): RedirectResponse
    {
        // dd($request->all());
        $cashbonKaryawan = CashbonKaryawan::find($cashbonKaryawan);



        // dd("current user id: " . $current_user_id);
        $store_data = [
            'karyawan_id' => $request->input('karyawan_id'),
            'nominal_cashbon' => $request->input('nominal_cashbon'),
            'tipe' => $request->input('tipe'),
            'keterangan' => $request->input('keterangan'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'karyawan_id' => ['required', 'integer', 'max:255'],
            'nominal_cashbon' => ['required', 'integer'],
            'tipe' => ['required', 'string', 'max:50'],
            'keterangan' => ['string',],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }


        // dd("validated data: " . json_encode($validate));




        // dd($data);

        $cashbonKaryawan->update($store_data);


        // dd("cashbonKaryawan updated: " . json_encode($cashbonKaryawan));



        return to_route('cashbon_karyawans.index')->with('status', 'CashbonKaryawan updated successfully.');
    }

    //soft delete
    public function destroy(int $cashbonKaryawan): RedirectResponse
    {
        $cashbonKaryawan = CashbonKaryawan::find($cashbonKaryawan);
        $cashbonKaryawan->update(['deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('cashbonkaryawans.index')->with('status', 'CashbonKaryawan deleted successfully.');
    }
}
