<?php

return [
    [
        'title' => 'Dashboard',
        'icon' => 'fas-house',
        'route' => 'dashboard',
        'active' => 'dashboard*',
        'permission' => null, // null berarti bisa diakses semua user yang login
    ],
    [
        'title' => 'User Management',
        'icon' => 'fas-users',
        'active' => ['users*', 'roles*', 'permissions*'],
        'permission' => ['view-users', 'view-roles', 'view-permissions'], // parent tampil jika punya salah satu
        'children' => [
            [
                'title' => 'Users',
                'icon' => 'fas-user',
                'route' => 'users.index',
                'active' => 'users*',
                'permission' => 'view-users',
            ],
            [
                'title' => 'Roles',
                'icon' => 'fas-shield',
                'route' => 'roles.index',
                'active' => 'roles*',
                'permission' => 'view-roles',
            ],
            [
                'title' => 'Permissions',
                'icon' => 'fas-key',
                'route' => 'permissions.index',
                'active' => 'permissions*',
                'permission' => 'view-permissions',
            ],
        ],
    ],
    [
        'title' => 'Setting',
        'icon' => 'fas-cog',
        'active' => 'dinamisvariables*',
        'permission' => 'view-dinamisvariables', 
        'children' => [
            [
                'title' => 'Dinamic Variable',
                'icon' => 'fas-cogs',
                'route' => 'dinamisvariables.index',
                'active' => 'dinamisvariables*',
                'permission' => 'view-dinamisvariables',
            ],
        ],
    ],
    [
        'title' => 'Entitas',
        'icon' => 'fas-users-cog',
        'active' => ['suppliers*', 'customers*', 'pihak3s*', 'karyawans*'],
        'permission' => ['view-suppliers', 'view-customers', 'view-pihak3s', 'view-karyawans'],
        'children' => [
            [
                'title' => 'Supplier',
                'icon' => 'fas-truck',
                'route' => 'suppliers.index',
                'active' => 'suppliers*',
                'permission' => 'view-suppliers',
            ],
            [
                'title' => 'Customer',
                'icon' => 'fas-user-tie',
                'route' => 'customers.index',
                'active' => 'customers*',
                'permission' => 'view-customers',
            ],
            [
                'title' => 'Pihak 3',
                'icon' => 'fas-handshake',
                'route' => 'pihak3s.index',
                'active' => 'pihak3s*',
                'permission' => 'view-pihak3s',
            ],
            [
                'title' => 'Karyawan',
                'icon' => 'fas-id-card',
                'route' => 'karyawans.index',
                'active' => 'karyawans*',
                'permission' => 'view-karyawans',
            ],
        ],
    ],
    [
        'title' => 'Cashbon',
        'icon' => 'fas-money-bill-wave',
        'active' => ['cashbonkaryawans*', 'cashbonkaryawanpembayarans*', 'cashbonsuppliers*', 'cashbonsupplierpembayarans*'],
        'permission' => ['view-cashbonkaryawans', 'view-cashbonsuppliers'],
        'children' => [
            [
                'title' => 'Karyawan',
                'icon' => 'fas-person',
                'active' => ['cashbonkaryawans*', 'cashbonkaryawanpembayarans*'],
                'permission' => 'view-cashbonkaryawans',
                'children' => [ // Mendukung Level 3
                    [
                        'title' => '-- Cashbon',
                        'route' => 'cashbonkaryawans.index',
                        'active' => 'cashbonkaryawans*',
                        'permission' => 'view-cashbonkaryawans',
                    ],
                    [
                        'title' => '-- Pembayaran Cashbon',
                        'route' => 'cashbonkaryawanpembayarans.index',
                        'active' => 'cashbonkaryawanpembayarans*',
                        'permission' => 'view-cashbonkaryawans',
                    ],
                ],
            ],
            [
                'title' => 'Supplier',
                'icon' => 'fas-person',
                'active' => ['cashbonsuppliers*', 'cashbonsupplierpembayarans*'],
                'permission' => 'view-cashbonsuppliers',
                'children' => [
                    [
                        'title' => '-- Cashbon',
                        'route' => 'cashbonsuppliers.index',
                        'active' => 'cashbonsuppliers*',
                        'permission' => 'view-cashbonsuppliers',
                    ],
                    [
                        'title' => '-- Pembayaran Cashbon',
                        'route' => 'cashbonsupplierpembayarans.index',
                        'active' => 'cashbonsupplierpembayarans*',
                        'permission' => 'view-cashbonsuppliers',
                    ],
                ],
            ],
        ],
    ],
    [
        'title' => 'Titipan',
        'icon' => 'fas-wallet',
        'active' => ['titipsupplier*', 'ambilsupplier*'],
        'permission' => ['view-titip-suppliers', 'view-ambil-suppliers'],
        'children' => [
            [
                'title' => 'Titipan Supplier',
                'icon' => 'fas-wallet',
                'route' => 'titipsuppliers.index',
                'active' => 'titipsuppliers*',
                'permission' => 'view-titip-suppliers',
            ],
            [
                'title' => 'Ambil Supplier',
                'icon' => 'fas-exchange-alt',
                'route' => 'ambilsuppliers.index',
                'active' => 'ambilsuppliers*',
                'permission' => 'view-ambil-suppliers',
            ],
        ],
    ],
    [
        'title' => 'Produk',
        'icon' => 'fas-boxes-packing',
        'active' => ['produks*', 'kategoris*', 'stoks*', 'history_harga_bases*'],
        'permission' => ['view-produks', 'view-kategoris', 'view-stoks', 'view-history-harga'],
        'children' => [
            [
                'title' => 'Produk',
                'icon' => 'fas-cube',
                'route' => 'produks.index',
                'active' => 'produks*',
                'permission' => 'view-produks',
            ],
            [
                'title' => 'Kategori',
                'icon' => 'fas-tags',
                'route' => 'kategoris.index',
                'active' => 'kategoris*',
                'permission' => 'view-kategoris',
            ],
            [
                'title' => 'Stok',
                'icon' => 'fas-boxes-stacked',
                'route' => 'stoks.index',
                'active' => 'stoks*',
                'permission' => 'view-stoks',
            ],
            [
                'title' => 'Stok Titipan',
                'icon' => 'fas-boxes-stacked',
                'route' => 'stoktitipans.index',
                'active' => 'stoktitipans*',
                'permission' => 'view-stok-titipans',
            ],
            [
                'title' => 'History Harga',
                'icon' => 'fas-history',
                'route' => 'history_harga_bases.index',
                'active' => 'history_harga_bases*',
                'permission' => 'view-history_harga_bases',
            ],
        ],
    ],
    [
        'title' => 'Transaksi',
        'icon' => 'fas-exchange-alt',
        'active' => ['pembelians*', 'pengirimans*', 'penjualans*'],
        'permission' => ['view-pembelians', 'view-pengirimans', 'view-penjualans'],
        'children' => [
            [
                'title' => 'Pembelian',
                'icon' => 'fas-shopping-basket',
                'route' => 'pembelians.index',
                'active' => 'pembelians*',
                'permission' => 'view-pembelians',
            ],
            [
                'title' => 'Pengiriman',
                'icon' => 'fas-shipping-fast',
                'route' => 'pengirimans.index',
                'active' => 'pengirimans*',
                'permission' => 'view-pengirimans',
            ],
            [
                'title' => 'Penjualan',
                'icon' => 'fas-cash-register',
                'route' => 'penjualans.index',
                'active' => 'penjualans*',
                'permission' => 'view-penjualans',
            ],
        ],
    ],
    [
        'title' => 'Absensi',
        'icon' => 'fas-calendar-check',
        'active' => 'absensis*',
        'permission' => 'view-absensis',
        'children' => [
            [
                'title' => 'Absensi',
                'icon' => 'fas-user-clock',
                'route' => 'absensis.index',
                'active' => 'absensis*',
                'permission' => 'view-absensis',
            ],
        ],
    ],
    [
        'title' => 'Laporan',
        'icon' => 'fas-chart-line',
        'active' => ['laporanpembelians*', 'laporanpengirimans*', 'laporanpenjualans*'],
        'permission' => ['view-laporanpembelian', 'view-laporanpengiriman', 'view-laporanpenjualan'],
        'children' => [
            [
                'title' => 'Laporan Pembelian',
                'icon' => 'fas-file-invoice-dollar',
                'route' => 'laporanpembelians.index',
                'active' => 'laporanpembelians*',
                'permission' => 'view-laporanpembelian',
            ],
            [
                'title' => 'Laporan Pengiriman',
                'icon' => 'fas-clipboard-list',
                'route' => 'laporanpengirimans.index',
                'active' => 'laporanpengirimans*',
                'permission' => 'view-laporanpengiriman',
            ],
            [
                'title' => 'Laporan Penjualan',
                'icon' => 'fas-file-medical-alt',
                'route' => 'laporanpenjualans.index',
                'active' => 'laporanpenjualans*',
                'permission' => 'view-laporanpenjualan',
            ],
            [
                'title' => 'DLL',
                'icon' => 'fas-file-invoice-dollar',
                'route' => 'dashboard',
                'active' => 'laporanpembelians*',
                'permission' => null,
            ],
            [
                'title' => 'DLL',
                'icon' => 'fas-file-invoice-dollar',
                'route' => 'dashboard',
                'active' => 'laporanpembelians*',
                'permission' => null,
            ],
            [
                'title' => 'DLL',
                'icon' => 'fas-file-invoice-dollar',
                'route' => 'dashboard',
                'active' => 'laporanpembelians*',
                'permission' => null,
            ],
            
        ],
    ],
];