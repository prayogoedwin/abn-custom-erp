<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ $title }}</span>
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage System {{ $title }}</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('download-' . $tablename))
            <a href="{{ route(strtolower($tablename) . '.export') }}">
                <x-button type="secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Download Excel') }}
                </x-button>
            </a>
            @endif
            @if(auth()->user()->hasPermission('create-' . strtolower($tablename)))
            <a href="{{ route(strtolower($tablename) . '.create') }}">
                <x-button type="primary">{{ __('Create ' . $title) }}</x-button>
            </a>
            @endif


        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="mb-4 flex flex-wrap gap-4 items-end bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
            <form action="{{ route($tablename . '.absensi') }}" method="get">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                    <select name="month" id="filter-month" class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                    <select name="year" id="filter-year" class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y') + 3; $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <x-button type="primary" class="h-9">GO</x-button>
            </form>
        </div>
        @if(!$isExist)
        {{-- Jika data kosong --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Data rekap untuk periode <strong>{{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</strong> belum tersedia.
                        </p>
                    </div>
                </div>

                {{-- Form untuk men-trigger fungsi generate --}}
                <form action="{{ route($tablename . '.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <x-button type="primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Buat Rekap Absensi
                    </x-button>
                </form>
            </div>
        </div>
        @else
        <div class="p-4">
            <table id="dynamic-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        @foreach($columns as $column)
                        @if($column['intable'])
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $column['title'] }}</th>
                        @endif
                        @endforeach

                        @if($tableaction)
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
        @endif
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>


    <script>
        $columnsdata = [
            @foreach($columns as $column)
            @if($column["intable"]) {
                data: '{{ $column["value"] }}'
            },
            @endif
            @endforeach
            @if($tableaction) {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-right whitespace-nowrap'
            }
            @endif


        ];

        console.log('$columnsdata:', $columnsdata);

        $(document).ready(function() {
            $('#dynamic-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route(strtolower($tablename) . ".absensi") }}',
                columns: $columnsdata,
                order: [
                    [0, 'desc']
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search " + "{{ $title }}",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ {{ strtolower($title) }}",
                    infoEmpty: "No {{ strtolower($title) }} found",
                    infoFiltered: "(filtered from _MAX_ total {{ strtolower($title) }})",
                    zeroRecords: "No matching {{ strtolower($title) }} found",
                    emptyTable: "No {{ strtolower($title) }} available"
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
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            @apply px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            @apply px-3 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 mx-1;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            @apply bg-blue-600 text-white border-blue-600;
        }

        .dataTables_wrapper .dataTables_info {
            @apply text-sm text-gray-600 dark:text-gray-400;
        }
    </style>
</x-layouts.app>