<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e40af; padding-bottom: 12px; }
        .header h2 { font-size: 14px; font-weight: bold; color: #1e40af; letter-spacing: 0.5px; }
        .header p { font-size: 10px; color: #64748b; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead tr { background-color: #1e40af; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-size: 10px; font-weight: 600; }
        tbody tr:nth-child(even) { background-color: #f1f5f9; }
        tbody tr:nth-child(odd) { background-color: #ffffff; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
        .badge-hadir { color: #166534; font-weight: 600; }
        .no-col { width: 4%; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAP KEHADIRAN RELAWAN</h2>
        <h2>SPPG CURUG CUKANGGALIH 02</h2>
        <p>Periode: {{ $bulanLabel }} &nbsp;&bull;&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th>Nama Relawan</th>
                <th>NIK</th>
                <th>Divisi</th>
                <th style="text-align:center;">Hari Hadir</th>
                <th style="text-align:center;">Total Jam Kerja</th>
                <th style="text-align:center;">Rata-rata Jam/Hari</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $relawan)
            @php
                $totalHari = $relawan->attendances->whereNotNull('check_in_at')->count();
                $totalJam  = (float) $relawan->attendances->sum('total_hours');
                $rataRata  = $totalHari > 0 ? round($totalJam / $totalHari, 2) : 0;
            @endphp
            <tr>
                <td class="no-col" style="text-align:center;">{{ $i + 1 }}</td>
                <td><strong>{{ $relawan->name }}</strong></td>
                <td>{{ $relawan->nik ?? '-' }}</td>
                <td>{{ $relawan->division?->name ?? 'Umum' }}</td>
                <td style="text-align:center;" class="badge-hadir">{{ $totalHari }} hari</td>
                <td style="text-align:center;">{{ number_format($totalJam, 2) }} jam</td>
                <td style="text-align:center;">{{ number_format($rataRata, 2) }} jam</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding: 20px; color: #94a3b8;">Tidak ada data kehadiran untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh Sistem Absensi SPPG Curug Cukanggalih 02 &mdash; {{ now()->format('d/m/Y H:i:s') }} WIB
    </div>
</body>
</html>
