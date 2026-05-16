            <aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
                class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
                        <ul class="space-y-1 px-2">
                            <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                                :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>

                            <x-layouts.sidebar-two-level-link-parent title="User Management" icon="fas-users"
                                :active="request()->routeIs('users*') || request()->routeIs('roles*') || request()->routeIs('permissions*')">
                                <x-layouts.sidebar-two-level-link href="{{ route('users.index') }}" icon='fas-user'
                                    :active="request()->routeIs('users*')">Users</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('roles.index') }}" icon='fas-shield'
                                    :active="request()->routeIs('roles*')">Roles</x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="{{ route('permissions.index') }}" icon='fas-key'
                                    :active="request()->routeIs('permissions*')">Permissions</x-layouts.sidebar-two-level-link>
                            </x-layouts.sidebar-two-level-link-parent>



                            <x-layouts.sidebar-two-level-link-parent title="Menu Setting" icon="fas-cog"
                                :active="request()->routeIs('dinamisvariables*')">

                                <x-layouts.sidebar-two-level-link href="{{ route('dinamisvariables.index') }}" icon='fas-cogs'
                                    :active="request()->routeIs('dinamisvariables*')">Menu Dinamic Variable</x-layouts.sidebar-two-level-link>


                            </x-layouts.sidebar-two-level-link-parent>

                            <x-layouts.sidebar-two-level-link-parent title="Menu Entitas" icon="fas-users-cog"
                                :active="request()->routeIs('suppliers*') || request()->routeIs('customers*') || request()->routeIs('pihak3s*') || request()->routeIs('karyawans*')">

                                <x-layouts.sidebar-two-level-link href="{{ route('suppliers.index') }}" icon='fas-truck'
                                    :active="request()->routeIs('suppliers*')">Menu Supplier</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('customers.index') }}" icon='fas-user-tie'
                                    :active="request()->routeIs('customers*')">Menu Customer</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('pihak3s.index') }}" icon='fas-handshake'
                                    :active="request()->routeIs('pihak3s*')">Menu Pihak 3</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('karyawans.index') }}" icon='fas-id-card'
                                    :active="request()->routeIs('karyawans*')">Menu Karyawan</x-layouts.sidebar-two-level-link>
                            </x-layouts.sidebar-two-level-link-parent>



                            <x-layouts.sidebar-two-level-link-parent title="Menu Cashbon" icon="fas-house"
                                :active="request()->routeIs('cashbonkaryawans*') || request()->routeIs('cashbonkaryawanpembayarans*') || request()->routeIs('cashbonsuppliers*') || request()->routeIs('cashbonsupplierpembayarans*')">


                                <x-layouts.sidebar-three-level-parent title="Karyawan" icon="fas-house"
                                    :active="request()->routeIs('cashbonkaryawans*') || request()->routeIs('cashbonkaryawanpembayarans*')">


                                    <x-layouts.sidebar-three-level-link href="{{ route('cashbonkaryawans.index') }}" :active="request()->routeIs('cashbonkaryawans*')">
                                        Cashbon
                                    </x-layouts.sidebar-three-level-link>

                                    <x-layouts.sidebar-three-level-link href="{{ route('cashbonkaryawanpembayarans.index') }}" :active="request()->routeIs('cashbonkaryawanpembayarans*')">
                                        Pembayaran Cashbon
                                    </x-layouts.sidebar-three-level-link>
                                </x-layouts.sidebar-three-level-parent>

                                <x-layouts.sidebar-three-level-parent title="Supplier" icon="fas-house"
                                    :active="request()->routeIs('cashbonsuppliers*') || request()->routeIs('cashbonsupplierpembayarans*')">


                                    <x-layouts.sidebar-three-level-link href="{{ route('cashbonsuppliers.index') }}" :active="request()->routeIs('cashbonsuppliers*')">
                                        Cashbon
                                    </x-layouts.sidebar-three-level-link>

                                    <x-layouts.sidebar-three-level-link href="{{ route('cashbonsupplierpembayarans.index') }}" :active="request()->routeIs('cashbonsupplierpembayarans*')">
                                        Pembayaran Cashbon
                                    </x-layouts.sidebar-three-level-link>
                                </x-layouts.sidebar-three-level-parent>


                            </x-layouts.sidebar-two-level-link-parent>

                            <x-layouts.sidebar-two-level-link-parent title="Menu Produk" icon="fas-boxes-packing"
                                :active="request()->routeIs('produks*') || request()->routeIs('kategoris*') || request()->routeIs('stoks*') || request()->routeIs('historyhargabases*')">

                                <x-layouts.sidebar-two-level-link href="{{ route('produks.index') }}" icon='fas-cube'
                                    :active="request()->routeIs('produks*')">Menu Produk</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('kategoris.index') }}" icon='fas-tags'
                                    :active="request()->routeIs('kategoris*')">Menu Kategori</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('stoks.index') }}" icon='fas-boxes-stacked'
                                    :active="request()->routeIs('stoks*')">Menu Stok</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('history_harga_bases.index') }}" icon='fas-history'
                                    :active="request()->routeIs('history_harga_bases*')">History Harga</x-layouts.sidebar-two-level-link>

                            </x-layouts.sidebar-two-level-link-parent>

                            <!-- Menu Transaksi -->
                            <x-layouts.sidebar-two-level-link-parent title="Menu Transaksi" icon="fas-exchange-alt"
                                :active="request()->routeIs('pembelians*') || request()->routeIs('pengirimans*') || request()->routeIs('penjualans*')">

                                <x-layouts.sidebar-two-level-link href="{{ route('pembelians.index') }}" icon='fas-shopping-basket'
                                    :active="request()->routeIs('pembelians*')">Pembelian</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('pengirimans.index') }}" icon='fas-shipping-fast'
                                    :active="request()->routeIs('pengirimans*')">Pengiriman</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('penjualans.index') }}" icon='fas-cash-register'
                                    :active="request()->routeIs('penjualans*')">Penjualan</x-layouts.sidebar-two-level-link>

                            </x-layouts.sidebar-two-level-link-parent>






                            <x-layouts.sidebar-two-level-link-parent title="Menu Absensi" icon="fas-calendar-check"
                                :active="request()->routeIs('absensis*')">

                                <x-layouts.sidebar-two-level-link href="{{ route('absensis.index') }}" icon='fas-user-clock'
                                    :active="request()->routeIs('absensis*')">Menu Absensi</x-layouts.sidebar-two-level-link>

                            </x-layouts.sidebar-two-level-link-parent>

                            <!-- Menu Laporan -->
                            <x-layouts.sidebar-two-level-link-parent title="Menu Laporan" icon="fas-chart-line"
                                :active="request()->routeIs('laporanpembelians*') || request()->routeIs('laporanpengirimans*') || request()->routeIs('laporanpenjualans*')">

                                <x-layouts.sidebar-two-level-link href="{{ route('laporanpembelians.index') }}" icon='fas-file-invoice-dollar'
                                    :active="request()->routeIs('laporanpembelians*')">Laporan Pembelian</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('laporanpengirimans.index') }}" icon='fas-clipboard-list'
                                    :active="request()->routeIs('laporanpengirimans*')">Laporan Pengiriman</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="{{ route('laporanpenjualans.index') }}" icon='fas-file-medical-alt'
                                    :active="request()->routeIs('laporanpenjualans*')">Laporan Penjualan</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-two-level-link href="#" icon='fas-clipboard'
                                    :active="request()->routeIs('laporanpenjualans*')">
                                    DLL
                                </x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-clipboard'
                                    :active="request()->routeIs('laporanpenjualans*')">
                                    DLL
                                </x-layouts.sidebar-two-level-link>
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-clipboard'
                                    :active="request()->routeIs('laporanpenjualans*')">
                                    DLL
                                </x-layouts.sidebar-two-level-link>

                            </x-layouts.sidebar-two-level-link-parent>

                            <x-layouts.sidebar-link href="" icon='fas-clipboard'
                                >DLL</x-layouts.sidebar-link>
                            <x-layouts.sidebar-link href="" icon='fas-clipboard'
                                >DLL</x-layouts.sidebar-link>
                            <x-layouts.sidebar-link href="" icon='fas-clipboard'
                                >DLL</x-layouts.sidebar-link>


                        </ul>
                    </nav>
                </div>
            </aside>