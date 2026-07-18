<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-users',
            'show-users',
            'create-users',
            'edit-users',
            'download-users',
            'delete-users',
            'view-roles',
            'show-roles',
            'create-roles',
            'edit-roles',
            'download-roles',
            'delete-roles',
            'view-permissions',
            'show-permissions',
            'create-permissions',
            'edit-permissions',
            'download-permissions',
            'delete-permissions',
            // Produk permissions
            'view-produks',
            'show-produks',
            'create-produks',
            'edit-produks',
            'download-produks',
            'delete-produks',
                // Kategori permissions
            'view-kategoris',
            'show-kategoris',
            'create-kategoris',
            'edit-kategoris',
            'update-kategoris',
            'download-kategoris',
            'delete-kategoris',
            //Supplier permission
            'view-suppliers',
            'show-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'update-suppliers',
            'download-suppliers',
            'delete-suppliers',

            //stoks permission
            'view-stoks',
            'show-stoks',
            // 'create-stoks',
            // 'edit-stoks',
            // 'update-stoks',
            'download-stoks',
            // 'delete-stoks',

            //history_harga_bases permission
            'view-history-harga',
            'show-history-harga',
            // 'create-history_harga_bases',
            // 'edit-history_harga_bases',
            // 'update-history_harga_bases',
            'download-history-harga',
            // 'delete-history_harga_bases',

            //customer permission
            'view-customers',
            'show-customers',
            'create-customers',
            'edit-customers',
            'update-customers',
            'download-customers',
            'delete-customers',
            //pihak3s permission
            'view-pihak3s',
            'show-pihak3s',
            'create-pihak3s',
            'edit-pihak3s',
            'update-pihak3s',
            'download-pihak3s',
            'delete-pihak3s',
            //karyawans permission
            'view-karyawans',
            'show-karyawans',
            'create-karyawans',
            'edit-karyawans',
            'update-karyawans',
            'download-karyawans',
            'delete-karyawans',

            //cashbonkaryawans
            'view-cashbonkaryawans',
            'show-cashbonkaryawans',
            'create-cashbonkaryawans',
            'edit-cashbonkaryawans',
            'update-cashbonkaryawans',
            'download-cashbonkaryawans',
            'delete-cashbonkaryawans',

            //cashbonkaryawanpembayarans
            'view-cashbonkaryawanpembayarans',
            'show-cashbonkaryawanpembayarans',
            'create-cashbonkaryawanpembayarans',
            'edit-cashbonkaryawanpembayarans',
            'update-cashbonkaryawanpembayarans',
            'download-cashbonkaryawanpembayarans',
            'delete-cashbonkaryawanpembayarans',

            //cashbonsuppliers
            'view-cashbonsuppliers',
            'show-cashbonsuppliers',
            'create-cashbonsuppliers',
            'edit-cashbonsuppliers',
            'update-cashbonsuppliers',
            'download-cashbonsuppliers',
            'delete-cashbonsuppliers',

            //cashbonsupplierpembayarans
            'view-cashbonsupplierpembayarans',
            'show-cashbonsupplierpembayarans',
            'create-cashbonsupplierpembayarans',
            'edit-cashbonsupplierpembayarans',
            'update-cashbonsupplierpembayarans',
            'download-cashbonsupplierpembayarans',
            'delete-cashbonsupplierpembayarans',

            //dinamisvariables
            'view-dinamisvariables',
            'show-dinamisvariables',
            'create-dinamisvariables',
            'edit-dinamisvariables',
            'update-dinamisvariables',
            'download-dinamisvariables',
            'delete-dinamisvariables',

            //pembelians
            'view-pembelians',
            'show-pembelians',
            'create-pembelians',
            'edit-pembelians',
            'update-pembelians',
            'download-pembelians',
            'delete-pembelians',

            //pembeliansdetails
            'view-pembeliandetails',
            'show-pembeliandetails',
            'create-pembeliandetails',
            'edit-pembeliandetails',
            'update-pembeliandetails',
            'download-pembeliandetails',
            'delete-pembeliandetails',

            //pengiriman
            'view-pengirimans',
            'show-pengirimans',
            'create-pengirimans',
            'edit-pengirimans',
            'update-pengirimans',
            'download-pengirimans',
            'delete-pengirimans',

            //pengirimansdetails
            'view-pengirimandetails',
            'show-pengirimandetails',
            'create-pengirimandetails',
            'edit-pengirimandetails',
            'update-pengirimandetails',
            'download-pengirimandetails',
            'delete-pengirimandetails',

            //penjualan
            'view-penjualans',
            'show-penjualans',
            'create-penjualans',
            'edit-penjualans',
            'update-penjualans',
            'download-penjualans',
            'delete-penjualans',

            //penjualansdetails
            'view-penjualandetails',
            'show-penjualandetails',
            'create-penjualandetails',
            'edit-penjualandetails',
            'update-penjualandetails',
            'download-penjualandetails',
            'delete-penjualandetails',


            //absensis
            'view-absensis',
            'show-absensis',
            'create-absensis',
            'edit-absensis',
            'update-absensis',
            'download-absensis',
            'delete-absensis',

            //laporans
            'view-laporanpengiriman',
            'view-laporanpenjualan',
            'view-laporanpembelian',


            'view-titip-suppliers',
            'show-titip-suppliers',
            'create-titip-suppliers',
            'edit-titip-suppliers',
            'update-titip-suppliers',
            'download-titip-suppliers',
            'delete-titip-suppliers',

            'view-ambil-suppliers',
            'show-ambil-suppliers',
            'create-ambil-suppliers',
            'edit-ambil-suppliers',
            'update-ambil-suppliers',
            'download-ambil-suppliers',
            'delete-ambil-suppliers',

            //stoks permission
            'view-stok-titipans',
            'show-stok-titipans',
            'create-stok-titipans',
            'edit-stok-titipans',
            'update-stok-titipans',
            'download-stok-titipans',
            'delete-stok-titipans',

        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $superAdminRole->permissions()->sync(Permission::all());
        $adminRole->permissions()->sync(Permission::all());

        $editorRole->permissions()->sync(
            Permission::whereIn('name', [
                'view-users', 'show-users',
                'view-roles', 'show-roles',
                'view-permissions', 'show-permissions'
            ])->pluck('id')
        );

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Password!@#26'),
            ]
        );

        $superAdmin->roles()->sync([$superAdminRole->id]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->roles()->sync([$adminRole->id]);

        $editor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
            ]
        );

        $editor->roles()->sync([$editorRole->id]);

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
            ]
        );

        $user->roles()->sync([$userRole->id]);
    }
}
