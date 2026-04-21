<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{
    private function getPagedata()
    {

        $pagedata = [
            'title' => 'Absensi',
            'tablename' => 'absensis',
            'tableaction' => true,
            'columns' => [
                ['name' => 'user_name', 'value' => 'user_name',  'title' => 'Nama Absensi', 'type' => 'text', 'inform' => false, 'inshow' => true,  'intable' => true],
                ['name' => 'bulan', 'value' => 'bulan',  'title' => 'Bulan', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => false],
                ['name' => 'tahun', 'value' => 'tahun',  'title' => 'Tahun', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => false],
                ['name' => 'jumlah_masuk', 'value' => 'jumlah_masuk',  'title' => 'Jumlah Masuk', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'jumlah_absen', 'value' => 'jumlah_absen',  'title' => 'Jumlah Izin', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'jumlah_izin', 'value' => 'jumlah_izin',  'title' => 'Jumlah Izin', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],

            ],
        ];

        return $pagedata;
    }

    public function index()
    {
        $pagedata = $this->getPagedata();

        return view('absensis.index', $pagedata);
    }

    public function absensi(Request $request)
    {
        // dd($request->all());

        
        $month = $request->month;
        $year = $request->year;

        $absensis = Absensi::join('karyawans', 'absensis.karyawan_id', '=', 'karyawans.id')->join('users', 'karyawans.user_id', '=', 'users.id')
            // Select everything from absensi, and specific fields from users
            ->select('absensis.*', 'users.name as user_name', 'users.email')
            
            
            ->get();
        // dd($absensis);


        if ($request->ajax()) {

            // dd('masuk ajax');
            $absensis = Absensi::join('karyawans', 'absensis.karyawan_id', '=', 'karyawans.id')->join('users', 'karyawans.user_id', '=', 'users.id')
                // Select everything from absensi, and specific fields from users
                ->select('absensis.*', 'users.name as user_name', 'users.email')
                ->where('absensis.isactive', true)
                
                ->get();
            // dd($absensis);

            return DataTables::of($absensis)
               



                ->addColumn('actions', function ($absensi) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-absensis')) {
                        $actions .= '<a href="' . route('absensis.show', $absensi) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-absensis')) {
                        $actions .= '<a href="' . route('absensis.edit', $absensi) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-absensis')) {
                        $actions .= '<form action="' . route('absensis.destroy', $absensi) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }



        $rekapData = Absensi::where('bulan', $month)
            ->where('tahun', $year)
            ->get();

        $isExist = $rekapData->isNotEmpty();


        $pagedata = $this->getPagedata();

        return view('absensis.absensi', compact('isExist', 'month', 'year'), $pagedata);
    }

    public function generate(Request $request): RedirectResponse
    {

        $month = $request->month;
        $year = $request->year;

        $karyawans = Karyawan::where('isactive', true)->get();

        foreach ($karyawans as $karyawan) {


            // 3. Simpan ke table rekap
            Absensi::create([
                'karyawan_id'  => $karyawan->id,
                'nama'         => $karyawan->nama,
                'bulan'        => $month,
                'tahun'        => $year,
                'jumlah_masuk' => 0,
                'jumlah_absen' => 0,
                'jumlah_izin'  => 0,
                'isactive'     => 1,
                'created_by'   => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Rekap bulan ini berhasil digenerate!');
    }





    public function export()
    {
        return Excel::download(new AbsensiExport, 'absensis-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],
        ]);

        // 2. Add the extra data after validation
        $validated['created_by'] = auth()->id();







        $absensi = Absensi::create($validated);
        // // log stok change
        // Stok::create([
        //     'absensi_id' => $absensi->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'absensi_id' => $absensi->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('absensis.index')->with('status', 'Absensi updated successfully.');
    }

    public function show(Absensi $absensi): View
    {
        $absensi->email = User::find($absensi->user_id)->email;

        $data = $absensi;


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe

        // dd($data);

        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Absensi $absensi): View
    {
        


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'kontak' => $request->input('kontak'),
            'alamat' => $request->input('alamat'),

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::default()],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }

        $user = User::find($absensi->user_id);

        $user->update(
            [
                'name' => $store_data['nama'],
                'email' => $store_data['email'],
            ]
        );

        if (! empty($store_data['password'])) {
            $user->update([
                'password' => Hash::make($store_data['password']),
            ]);
        }

        $absensi->update($store_data);



        // dd("absensi updated: " . json_encode($absensi));



        return to_route('absensis.index')->with('status', 'Absensi updated successfully.');
    }

    //soft delete
    public function destroy(Absensi $absensi): RedirectResponse
    {
        $absensi->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('absensis.index')->with('status', 'Absensi deleted successfully.');
    }
}
