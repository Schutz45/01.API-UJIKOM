<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Peminjaman & Pengembalian</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0;
        }

        .periode {
            margin-bottom: 20px;
        }

        h2 {
            font-size: 16px;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 15px;
            }
        }
    </style>
</head>

<body>

    {{-- Tombol hanya muncul di browser, tidak ikut tercetak --}}
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">
            Cetak Laporan
        </button>

        <button onclick="window.close()">
            Kembali
        </button>
    </div>


    {{-- HEADER --}}
    <div class="header">

        <h1>
            LAPORAN PEMINJAMAN DAN PENGEMBALIAN
        </h1>

        <p>
            SISTEM PEMINJAMAN ALAT
        </p>

        <p>
            SMK
        </p>

    </div>


    {{-- PERIODE --}}
    <div class="periode">

        <strong>Periode:</strong>

        @if($dari && $sampai)

            {{ \Carbon\Carbon::parse($dari)->format('d-m-Y') }}
            s/d
            {{ \Carbon\Carbon::parse($sampai)->format('d-m-Y') }}

        @elseif($dari)

            Mulai {{ \Carbon\Carbon::parse($dari)->format('d-m-Y') }}

        @elseif($sampai)

            Sampai {{ \Carbon\Carbon::parse($sampai)->format('d-m-Y') }}

        @else

            Semua Data

        @endif

    </div>


    {{-- LAPORAN PEMINJAMAN --}}
    <h2>
        A. LAPORAN PEMINJAMAN
    </h2>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Tanggal Pinjam</th>
                <th>Batas Kembali</th>
                <th>Alat</th>
                <th>Status</th>
            </tr>

        </thead>

        <tbody>

            @forelse($peminjamans as $item)

                <tr>

                    <td class="center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->user->name ?? '-' }}
                    </td>

                    <td>
                        {{ $item->tgl_pinjam }}
                    </td>

                    <td>
                        {{ $item->tgl_kembali_plan }}
                    </td>

                    <td>

                        @foreach($item->detailPinjam as $detail)

                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                            ({{ $detail->jumlah }})

                            @if(!$loop->last)
                                <br>
                            @endif

                        @endforeach

                    </td>

                    <td class="center">
                        {{ ucfirst($item->status) }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="center">
                        Tidak ada data peminjaman.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- LAPORAN PENGEMBALIAN --}}
    <h2>
        B. LAPORAN PENGEMBALIAN
    </h2>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Kondisi</th>
                <th>Denda Keterlambatan</th>
                <th>Denda Kerusakan</th>
                <th>Total Denda</th>
                <th>Petugas</th>
            </tr>

        </thead>

        <tbody>

            @forelse($pengembalians as $item)

                <tr>

                    <td class="center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->peminjaman->user->name ?? '-' }}
                    </td>

                    <td>
                        {{ $item->peminjaman->tgl_pinjam ?? '-' }}
                    </td>

                    <td>
                        {{ $item->tgl_kembali ?? '-' }}
                    </td>

                    <td class="center">
                        {{ ucfirst($item->kondisi_kembali) }}
                    </td>

                    <td>
                        Rp {{ number_format($item->denda_keterlambatan ?? 0, 0, ',', '.') }}
                    </td>

                    <td>
                        Rp {{ number_format($item->denda_kerusakan ?? 0, 0, ',', '.') }}
                    </td>

                    <td>
                        Rp {{ number_format($item->denda ?? 0, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ $item->petugas->name ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="9" class="center">
                        Tidak ada data pengembalian.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- FOOTER --}}
    <div class="footer">

        <p>
            Dicetak pada:
            {{ now()->format('d-m-Y H:i') }}
        </p>

        <br><br>

        <p>
            Petugas
        </p>

    </div>

</body>

</html>