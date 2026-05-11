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
        $karyawans = Karyawan::where('isactive', true)->get();

        $pagedata = [
            'title' => 'Absensi',
            'tablename' => 'absensis',
            'tableaction' => true,
            'columns' => [
                ['name' => 'karyawan_id', 'value' => 'user_name',  'title' => 'Nama Karyawan', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database

                    ...$karyawans->map(function ($karyawan) {
                        return ['value' => $karyawan->id, 'label' => $karyawan->nama];
                    })->toArray(),
                ]],
                ['name' => 'bulan', 'value' => 'bulan',  'title' => 'Bulan', 'type' => 'select', 'inform' => true, 'intable' => false, 'options' => [
                    ['value' => '1', 'label' => 'Januari'],
                    ['value' => '2', 'label' => 'Februari'],
                    ['value' => '3', 'label' => 'Maret'],
                    ['value' => '4', 'label' => 'April'],
                    ['value' => '5', 'label' => 'Mei'],
                    ['value' => '6', 'label' => 'Juni'],
                    ['value' => '7', 'label' => 'Juli'],
                    ['value' => '8', 'label' => 'Agustus'],
                    ['value' => '9', 'label' => 'September'],
                    ['value' => '10', 'label' => 'Oktober'],
                    ['value' => '11', 'label' => 'November'],
                    ['value' => '12', 'label' => 'Desember'],
                ]],
                ['name' => 'tahun', 'value' => 'tahun',  'title' => 'Tahun', 'type' => 'number', 'inform' => true, 'inshow' => true, 'intable' => false],
                ['name' => 'jumlah_masuk', 'value' => 'jumlah_masuk',  'title' => 'Jumlah Masuk', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'jumlah_absen', 'value' => 'jumlah_absen',  'title' => 'Jumlah Absen', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],
                ['name' => 'jumlah_izin', 'value' => 'jumlah_izin',  'title' => 'Jumlah Izin', 'type' => 'text', 'inform' => true, 'inshow' => true, 'intable' => true],

            ],
        ];

        return $pagedata;
    }

    public function index()
    {
        $pagedata = $this->getPagedata();

        // Tahun default terpilih adalah tahun ini
        // Tahun max ada alah tahun ini + 1
        // Tahun min adalah sesuai tahun yang ada di database 

        $tahunMin = Absensi::min('tahun') ?? date('Y');

        $tahunMax = date('Y') + 1;

        $tahunSekarang = date('Y');

        $pagedata['tahunMin'] = $tahunMin;
        $pagedata['tahunMax'] = $tahunMax;
        $pagedata['tahunSekarang'] = $tahunSekarang;
        
        return view('absensis.index', $pagedata);
    }

    public function absensi(Request $request)
    {
        // dd($request->all());


        $month = $request->month;
        $year = $request->year;

        // $absensis = Absensi::join('karyawans', 'absensis.karyawan_id', '=', 'karyawans.id')->join('users', 'karyawans.user_id', '=', 'users.id')
        //     // Select everything from absensi, and specific fields from users
        //     ->select('absensis.*', 'users.name as user_name', 'users.email')
        //     ->where('absensis.isactive', true)
        //     ->where('absensis.bulan', $month)
        //     ->where('absensis.tahun', $year)
        //     ->get();
        // dd($absensis);


        if ($request->ajax()) {

            // dd('masuk ajax');
            $query = Absensi::join('karyawans', 'absensis.karyawan_id', '=', 'karyawans.id')->join('users', 'karyawans.user_id', '=', 'users.id')
                // Select everything from absensi, and specific fields from users
                ->select('absensis.*', 'users.name as user_name', 'users.email')
                ->where('absensis.isactive', true);
            // dd($absensis);

            // Tambahkan kondisi hanya jika filter ada
            if ($month) {
                $query->where('absensis.bulan', $month);
            }
            if ($year) {
                $query->where('absensis.tahun', $year);
            }

            $absensis = $query->get();

            return DataTables::of($absensis)




                ->addColumn('actions', function ($absensi) {
                    $actions = '';



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
        $pagedata['month'] = $month;
        $pagedata['year'] = $year;

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
            'karyawan_id' => ['required', 'integer'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2000'],
            'jumlah_masuk' => ['required', 'numeric'],
            'jumlah_absen' => ['required', 'numeric'],
            'jumlah_izin' => ['required', 'numeric'],
        ]);

        // 2. Add the extra data after validation
        $validated['created_by'] = auth()->id();

        //pastikan tidak ada data absensi untuk karyawan, bulan, dan tahun yang sama
        $existingAbsensi = Absensi::where('karyawan_id', $validated['karyawan_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->where('isactive', true)
            ->first();
        if ($existingAbsensi) {
            return back()->withErrors(['karyawan_id' => 'Absensi untuk karyawan ini pada bulan dan tahun yang sama sudah ada.'])->withInput();
        }

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



        // dd($data);

        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Absensi $absensi): View
    {
        $absensi->email = User::find($absensi->karyawan->user_id)->email;
        // $absensi->nama = Karyawan::find($absensi->karyawan_id)->nama;

        // dd($absensi);

        $data = $absensi;


        $pagedata = $this->getPagedata();

        return view('absensis.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'jumlah_masuk' => $request->jumlah_masuk,
            'jumlah_absen' => $request->jumlah_absen,
            'jumlah_izin' => $request->jumlah_izin,

            'updated_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'jumlah_masuk' => ['required', 'numeric'],
            'jumlah_absen' => ['required', 'numeric'],
            'jumlah_izin' => ['required', 'numeric'],

            'updated_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
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
