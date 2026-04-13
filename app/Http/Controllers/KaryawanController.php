<?php

namespace App\Http\Controllers;

use App\Exports\KaryawanExport;
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

class KaryawanController extends Controller
{
    private function getPagedata()
    {

        $pagedata = [
            'title' => 'Karyawan',
            'tablename' => 'karyawans',

            'columns' => [
                ['name' => 'nama', 'value' => 'nama',  'title' => 'Nama Karyawan', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'email', 'value' => 'email',  'title' => 'Email', 'type' => 'email', 'inform' => true, 'intable' => true],
                ['name' => 'password', 'value' => 'password',  'title' => 'Password', 'type' => 'password', 'inform' => true, 'intable' => false],
                ['name' => 'noPegawai', 'value' => 'noPegawai',  'title' => 'No Pegawai', 'type' => 'text', 'inform' => false, 'intable' => true],
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
            $karyawans = Karyawan::join('users', 'karyawans.user_id', '=', 'users.id')
                // Select everything from karyawan, and specific fields from users
                ->select('karyawans.*', 'users.name as user_name', 'users.email')
                ->where('karyawans.isactive', true)
                ->get();
            // dd($karyawans);

            return DataTables::of($karyawans)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('karyawans.nama_karyawan', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_karyawans.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($karyawan) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-karyawans')) {
                        $actions .= '<a href="' . route('karyawans.show', $karyawan) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-karyawans')) {
                        $actions .= '<a href="' . route('karyawans.edit', $karyawan) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-karyawans')) {
                        $actions .= '<form action="' . route('karyawans.destroy', $karyawan) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
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
        return Excel::download(new KaryawanExport, 'karyawans-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'kontak' => $request->input('kontak'),
            'alamat' => $request->input('alamat'),

            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::default()],
            'kontak' => ['required'],
            'alamat' => ['required', 'string', 'max:50'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }

        $user = User::create(
            [
                'name' => $store_data['nama'],
                'email' => $store_data['email'],
                'password' => Hash::make($store_data['password']),
            ]
        );


        $lastId = Karyawan::max('id') ?? 0;

        $nextId = $lastId + 1;
        $store_data['noPegawai'] = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $store_data['user_id'] = $user->id;

        $karyawan = Karyawan::create($store_data);
        // // log stok change
        // Stok::create([
        //     'karyawan_id' => $karyawan->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'karyawan_id' => $karyawan->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('karyawans.index')->with('status', 'Karyawan updated successfully.');
    }

    public function show(Karyawan $karyawan): View
    {
        $karyawan->email = User::find($karyawan->user_id)->email;

        $data = $karyawan;


        $pagedata = $this->getPagedata();

        //TO DO: asdfasdfwe

        // dd($data);

        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Karyawan $karyawan): View
    {
        $karyawan->email = User::find($karyawan->user_id)->email;

        $data = $karyawan;


        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Karyawan $karyawan): RedirectResponse
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

        $user = User::find($karyawan->user_id);

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

        $karyawan->update($store_data);



        // dd("karyawan updated: " . json_encode($karyawan));



        return to_route('karyawans.index')->with('status', 'Karyawan updated successfully.');
    }

    //soft delete
    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        $karyawan->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);


        return to_route('karyawans.index')->with('status', 'Karyawan deleted successfully.');
    }
}
