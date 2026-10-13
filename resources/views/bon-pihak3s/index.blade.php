<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>

        <span class="text-gray-500 dark:text-gray-400">{{ __('Kartu Bon Pihak 3') }}</span>
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Kartu Bon Pihak 3') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Manage System Kartu Bon Pihak 3') }}</p>
        </div>
        <div class="flex gap-2">
            <!-- Create Bon Pihak 3 Button Hanya muncul jika sudah memilih pihak 3 -->
            <div id="create-bon-pihak3" class="hidden">
                <a id="bon-pihak3-link"
                    href="{{ route('bon-pihak3s.create', ['pihak3_id' => '__PIHAK3_ID__']) }}">
                    <x-button type="primary">
                        {{ __('Tambah Bon Pihak 3') }}
                    </x-button>
                </a>
            </div>

        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Section Wrapper Form Filter -->
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">



            <div class="flex flex-col sm:flex-row sm:items-end gap-4 max-w-3xl mb-4">
                <!-- Supplier Select Group -->
                <div class="flex-1">
                        <label for="pihak3" class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">
                        Pihak 3
                    </label>
                    <div class="relative">
                        <select id="pihak3" name="pihak3" class="block w-full px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-colors">
                            <option value="">Pilih Pihak 3</option>
                            @foreach($pihak3s as $pihak3)
                            <option value="{{ $pihak3->id }}" {{ request('pihak3') == $pihak3->id ? 'selected' : '' }}>
                                {{ $pihak3->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>



            <div class="flex flex-col sm:flex-row sm:items-end gap-4 max-w-3xl">

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 pt-2 sm:pt-0">
                    <button type="submit" onclick="tableReload()"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all cursor-pointer h-[38px]">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z" />
                        </svg>
                        Terapkan
                    </button>



                </div>
            </div>
        </div>

        <!-- Current Filter Info Status -->
        <div class="px-5 py-3 bg-blue-50/50 dark:bg-blue-950/20 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span id="current-filter-info">

            </span>
        </div>
        <div class="p-4">
            <table id="dynamic-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>


                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keluar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sisa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>




                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>



    <script>
        $(document).ready(function() {
            $('#dynamic-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("bon-pihak3s.indexTable") }}',
                    data: function(d) {
                        d.supplier = $('#pihak3').val();

                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'masuk',
                        name: 'masuk'
                    },
                    {
                        data: 'keluar',
                        name: 'keluar'
                    },
                    {
                        data: 'sisa',
                        name: 'sisa'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }




                ],


                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search " + "Bon Pihak 3",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ Bon Pihak 3s",
                    infoEmpty: "No Bon Pihak 3s found",
                    infoFiltered: "(filtered from _MAX_ total Bon Pihak 3s)",
                    zeroRecords: "No matching Bon Pihak 3s found",
                    emptyTable: "No Bon Pihak 3s available"
                },
                dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4"ip>',
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                stripeClasses: ['bg-white dark:bg-gray-800', 'bg-gray-50 dark:bg-gray-900']
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            document.getElementById('current-filter-info').textContent = 'Mohon Pilih Pihak 3 Terlebih Dahulu';
        });

        function tableReload() {
            $('#dynamic-table').DataTable().ajax.reload();

            let pihak3 = $('#pihak3').val();
            let text = '';

            if (!pihak3) {
                text = 'Mohon Pilih Pihak 3 Terlebih Dahulu';
            } else {
                text = 'Menampilkan Buku Bon Pihak 3 untuk Pihak 3 <strong>' + ($('#pihak3 option:selected').text()) + '</strong>';
            }
            $('#current-filter-info').html(text);

            const container = document.getElementById('create-bon-pihak3');
            const link = document.getElementById('bon-pihak3-link');
            if (pihak3) {
                container.classList.remove('hidden');
                const url = new URL(link.href);
                url.searchParams.set('pihak3_id', pihak3);
                link.href = url.toString();
            } else {
                container.classList.add('hidden');
            }

            // Show the reset button
            document.getElementById('reset-filter-button').classList.remove('hidden');

        }

        function formatTanggalDDMMYYYY(tanggal) {
            const date = new Date(tanggal);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
    </script>

    <style>
        /* Table borders and styling */
        #dynamic-table {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        #dynamic-table thead th {
            border-bottom: 2px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .dark #dynamic-table thead th {
            border-bottom-color: #374151;
            background-color: #1f2937;
        }

        #dynamic-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .dark #dynamic-table tbody tr {
            border-bottom-color: #374151;
        }

        /* Alternating row colors (striping) */
        #dynamic-table tbody tr.odd {
            background-color: #ffffff;
        }

        #dynamic-table tbody tr.even {
            background-color: #f9fafb;
        }

        .dark #dynamic-table tbody tr.odd {
            background-color: #1f2937;
        }

        .dark #dynamic-table tbody tr.even {
            background-color: #111827;
        }

        #dynamic-table tbody tr:hover {
            background-color: #e5e7eb !important;
        }

        .dark #dynamic-table tbody tr:hover {
            background-color: #374151 !important;
        }

        #dynamic-table tbody td {
            border-right: 1px solid #e5e7eb;
            padding: 12px 24px;
        }

        .dark #dynamic-table tbody td {
            border-right-color: #374151;
        }

        #dynamic-table tbody td:last-child {
            border-right: none;
        }

        #dynamic-table thead th {
            border-right: 1px solid #e5e7eb;
        }

        .dark #dynamic-table thead th {
            border-right-color: #374151;
        }

        #dynamic-table thead th:last-child {
            border-right: none;
        }

        /* Action links styling - keep inline */
        #dynamic-table tbody td a,
        #dynamic-table tbody td form {
            display: inline;
            white-space: nowrap;
        }

        /* DataTables controls styling */
    </style>

</x-layouts.app>