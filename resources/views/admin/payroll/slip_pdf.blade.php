<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Honorarium - {{ $detail->user->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; margin: 15px; }
        .header { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .header-sub { font-size: 10px; color: #555; margin-top: 3px; }
        .meta-table { width: 100%; margin-bottom: 15px; font-size: 11px; }
        .meta-table td { padding: 2px 4px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table th, .data-table td { border: 1px solid #ccc; padding: 6px 8px; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .total-box { background-color: #e8f5e9; font-size: 13px; font-weight: bold; padding: 8px; border: 1px solid #c8e6c9; }
        .signature-table { width: 100%; margin-top: 25px; }
        .signature-box { text-align: center; width: 45%; }
        .signature-space { height: 50px; }
    </style>
</head>
<body>

<div class="header text-center">
    <div class="header-title">SPPG CURUG CUKANGGALIH 02</div>
    <div class="header-sub">Kecamatan Curug, Kabupaten Tangerang, Banten &bull; Telp / WA Pengurus</div>
    <div style="font-size: 12px; font-weight: bold; margin-top: 6px;">SLIP HONORARIUM RELAWAN DIGITAL</div>
</div>

<table class="meta-table">
    <tr>
        <td style="width: 18%;" class="fw-bold">Nama Relawan</td>
        <td style="width: 32%;">: {{ $detail->user->name }}</td>
        <td style="width: 18%;" class="fw-bold">Periode</td>
        <td style="width: 32%;">: {{ $detail->payroll->period_start->format('d/m/Y') }} s/d {{ $detail->payroll->period_end->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td class="fw-bold">NIK / No. Induk</td>
        <td>: {{ $detail->user->nik ?? '-' }}</td>
        <td class="fw-bold">Status Slip</td>
        <td>: {{ strtoupper($detail->payroll->status) }}</td>
    </tr>
    <tr>
        <td class="fw-bold">Divisi Penugasan</td>
        <td>: {{ $detail->user->division?->name ?? 'Relawan Umum' }}</td>
        <td class="fw-bold">No. WhatsApp</td>
        <td>: {{ $detail->user->phone ?? '-' }}</td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 8%;">No</th>
            <th>Komponen Honorarium</th>
            <th class="text-center" style="width: 25%;">Kuantitas / Jam</th>
            <th class="text-right" style="width: 25%;">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td>Honor Pokok Jam Kerja</td>
            <td class="text-center">{{ number_format($detail->total_hours, 1) }} Jam Kerja</td>
            <td class="text-right">{{ number_format($detail->base_salary, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-center">2</td>
            <td>Uang Transport Kehadiran</td>
            <td class="text-center">{{ $detail->total_days_attended }} Hari Hadir</td>
            <td class="text-right">{{ number_format($detail->transport_allowance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-center">3</td>
            <td>Uang Makan (Hadir &ge; 6 Jam)</td>
            <td class="text-center">-</td>
            <td class="text-right">{{ number_format($detail->meal_allowance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-center">4</td>
            <td>Insentif Lembur (> 8 Jam)</td>
            <td class="text-center">-</td>
            <td class="text-right">{{ number_format($detail->overtime_pay, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-center">5</td>
            <td>Potongan Keterlambatan / Lainnya</td>
            <td class="text-center">-</td>
            <td class="text-right">({{ number_format($detail->deductions, 0, ',', '.') }})</td>
        </tr>
    </tbody>
</table>

<div class="total-box">
    <table style="width: 100%;">
        <tr>
            <td>TOTAL HONOR BERSIH DITERIMA:</td>
            <td class="text-right" style="font-size: 14px;">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

<table class="signature-table">
    <tr>
        <td class="signature-box">
            <div>Penerima / Relawan,</div>
            <div class="signature-space"></div>
            <div class="fw-bold">{{ $detail->user->name }}</div>
        </td>
        <td style="width: 10%;"></td>
        <td class="signature-box">
            <div>Curug Cukanggalih, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Bendahara / Pengurus SPPG,</div>
            <div class="signature-space"></div>
            <div class="fw-bold">{{ $detail->payroll->approver?->name ?? 'Pengurus SPPG' }}</div>
        </td>
    </tr>
</table>

<div style="font-size: 8px; color: #888; margin-top: 15px; text-align: center;">
    Dokumen ini dicetak otomatis secara elektronik oleh Sistem Absensi & Payroll SPPG Curug Cukanggalih 02 &bull; Dokumen Sah Tanpa Tanda Tangan Basah
</div>

</body>
</html>

