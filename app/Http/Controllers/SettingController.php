<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class SettingController extends Controller
{
    private function getPagedata()
    {

        $pagedata = [
            'title' => 'Settings',
            'tablename' => 'settings',
            'tableaction' => true,
            'canCreate' => false,
            'columns' => [
                ['name' => 'key', 'value' => 'key',  'title' => 'Nama Variable Dinamis', 'type' => 'text', 'inform' => false, 'intable' => true],
                ['name' => 'description', 'value' => 'description',  'title' => 'Description', 'type' => 'text', 'inform' => false, 'intable' => true],
                ['name' => 'value', 'value' => 'value',  'title' => 'Value', 'type' => 'text', 'inform' => true, 'intable' => true],
                


            ],
        ];

        return $pagedata;
    }

    public function index(Request $request)
    {
        
        // dd($request->headers->all());
        if ($request->ajax()) {
            // dd('masuk ajax');
            $settings = Setting::all();
            // dd($dinamisvariables);

            return DataTables::of($settings)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('dinamisvariables.nama_dinamisvariable', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_dinamisvariables.nama', 'like', "%{$keyword}%");
                // })



                ->addColumn('actions', function ($setting) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-dinamisvariables')) {
                        $actions .= '<a href="' . route('settings.show', $setting) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-dinamisvariables')) {
                        $actions .= '<a href="' . route('settings.edit', $setting) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    

                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.index', $pagedata);
    }

    public function show(Setting $setting)
    {
        $data = $setting;
        $pagedata = $this->getPagedata();

        
        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Setting $setting)
    {
        $data = $setting;
        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Setting $setting)
    {
        
        $store_data = [
            
            'value' => $request->input('value'),
            
        ];


        $validate = Validator::make($store_data, [
            
            'value' => ['required', 'string', 'max:255'],
        ]);

        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }

        $setting->update($store_data);

        return to_route('settings.index')->with('status', 'Setting updated successfully.');
    }
}
