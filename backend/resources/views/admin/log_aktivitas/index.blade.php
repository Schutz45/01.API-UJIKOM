@extends('layouts.app')

@section('title', 'Log Aktivitas - Panel Admin')
@section('header-title', 'Log Aktivitas')

@section('content')

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- Header --}}
    <div class="p-4 sm:p-5 border-b border-gray-200 bg-gray-50">

        <h3 class="text-lg font-bold text-gray-800">
            Log Aktivitas
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Riwayat aktivitas pengguna dalam sistem.
        </p>

    </div>


    {{-- Tabel --}}
    <div class="w-full overflow-x-auto">

        <table class="min-w-[750px] w-full text-left border-collapse">

            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Pengguna
                    </th>

                    <th class="py-3 px-4 border-b">
                        Aktivitas
                    </th>

                    <th class="py-3 px-4 border-b whitespace-nowrap">
                        Waktu
                    </th>

                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($logs as $log)

                    <tr class="hover:bg-gray-50 transition">

                        {{-- Pengguna --}}
                        <td class="py-3 px-4 border-b font-medium text-gray-900 whitespace-nowrap">
                            {{ $log->user->name ?? 'User Dihapus' }}
                        </td>

                        {{-- Aktivitas --}}
                        <td class="py-3 px-4 border-b">
                            {{ $log->aktivitas }}
                        </td>

                        {{-- Waktu --}}
                        <td class="py-3 px-4 border-b text-xs text-gray-500 whitespace-nowrap">
                            {{ $log->created_at?->format('d-m-Y H:i:s') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="3"
                            class="py-8 text-center text-gray-500"
                        >
                            Belum ada aktivitas.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50">

        <div class="flex flex-col sm:flex-row
                    items-center justify-between
                    gap-3">

            {{-- Informasi Pagination --}}
            <div class="text-xs sm:text-sm text-gray-500 text-center sm:text-left">
                Menampilkan
                <span class="font-medium text-gray-700">
                    {{ $logs->firstItem() ?? 0 }}
                </span>
                -
                <span class="font-medium text-gray-700">
                    {{ $logs->lastItem() ?? 0 }}
                </span>
                dari
                <span class="font-medium text-gray-700">
                    {{ $logs->total() }}
                </span>
                aktivitas
            </div>

            {{-- Tombol Pagination --}}
            <div class="w-full sm:w-auto overflow-x-auto">
                <div class="flex justify-center sm:justify-end min-w-max">
                    {{ $logs->links() }}
                </div>
            </div>

        </div>

    </div>

</div>

@endsection