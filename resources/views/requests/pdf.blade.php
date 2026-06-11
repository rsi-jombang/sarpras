<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Permintaan - {{ $request->request_number }}</title>
    <style>
        @page {
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #fff;
            line-height: 1.5;
            position: relative;
            min-height: 100vh;
            padding: 40px 40px 55px;
        }

        .page-border {
            position: fixed;
            top: 12px;
            left: 12px;
            right: 12px;
            bottom: 12px;
            border: 1.5px solid #cbd5e1;
            border-radius: 0;
            pointer-events: none;
        }

        .letterhead {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 12px;
            border-bottom: 2.5px solid #1e3a5f;
            margin-bottom: 0;
        }

        .letterhead .logo-wrap {
            flex-shrink: 0;
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .letterhead .logo-wrap img {
            max-width: 65px;
            max-height: 65px;
        }

        .letterhead .rs-identity {
            flex: 1;
        }

        .letterhead .rs-identity .institution {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 0.5px;
        }

        .letterhead .rs-identity .sub {
            font-size: 9px;
            color: #475569;
            margin-top: 1px;
        }

        .letterhead .rs-identity .address {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
            line-height: 1.4;
        }

        .doc-title-section {
            text-align: center;
            padding: 14px 0 10px;
            margin-bottom: 16px;
        }

        .doc-title-section .title-main {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .doc-title-section .title-number {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
            font-weight: bold;
        }

        .doc-title-section .title-underline {
            width: 80px;
            height: 2.5px;
            background: #1e3a5f;
            margin: 6px auto 0;
        }

        .info-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
            background: #f8fafc;
        }

        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 3px 28px;
        }

        .info-item {
            min-width: 160px;
            flex: 1 0 40%;
        }

        .info-item .label {
            font-size: 7px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: bold;
        }

        .info-item .value {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
        }

        .info-item .value-light {
            font-weight: normal;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1.5px solid #1e3a5f;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9px;
        }

        thead tr {
            background: #1e3a5f;
            color: #fff;
        }

        thead th {
            padding: 6px 7px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 0.5px solid #1e3a5f;
        }

        thead th.text-center { text-align: center; }
        thead th.text-right  { text-align: right; }

        tbody tr {
            border-bottom: 0.5px solid #e2e8f0;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody td {
            padding: 5px 7px;
            vertical-align: middle;
            border: 0.5px solid #e2e8f0;
        }

        tbody td.text-center { text-align: center; }
        tbody td.text-right  { text-align: right; }

        .status-label {
            display: inline-block;
            padding: 1px 8px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 0.5px solid;
        }

        .status-label.pending   { background: #fef9c3; color: #713f12; border-color: #facc15; }
        .status-label.approved  { background: #e0f2fe; color: #075985; border-color: #38bdf8; }
        .status-label.partial   { background: #e0f2fe; color: #075985; border-color: #38bdf8; }
        .status-label.rejected  { background: #fee2e2; color: #991b1b; border-color: #ef4444; }
        .status-label.ready     { background: #e0e7ff; color: #3730a3; border-color: #818cf8; }
        .status-label.completed { background: #dcfce7; color: #166534; border-color: #4ade80; }
        .status-label.cancelled { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }

        .notes-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 14px;
            background: #fafafa;
        }

        .notes-box .notes-label {
            font-size: 7px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: bold;
        }

        .notes-box .notes-text {
            font-size: 9px;
            color: #1e293b;
            margin-top: 2px;
        }

        .notes-box.reject {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .notes-box.reject .notes-label {
            color: #dc2626;
        }

        .notes-box.reject .notes-text {
            color: #991b1b;
        }

    .signature-section {
        margin-top: 50px;
    }

        .page-footer {
            position: fixed;
            bottom: 14px;
            left: 40px;
            right: 40px;
            font-size: 7px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 4px;
        }

        .text-muted { color: #94a3b8; }
        .fw-bold { font-weight: bold; }
        .mt-2 { margin-top: 4px; }
        .d-block { display: block; }
    </style>
</head>
<body>

    <div class="page-border"></div>

    {{-- Letterhead --}}
    <div class="letterhead">
        <div class="logo-wrap">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo RS">
            @endif
        </div>
        <div class="rs-identity">
            <div class="institution">RUMAH SAKIT</div>
            <div class="sub">Sistem Manajemen Sarana &amp; Prasarana</div>
            <div class="address">
                Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota — Telp. (021) 1234567
            </div>
        </div>
    </div>

    {{-- Document Title --}}
    <div class="doc-title-section">
        <div class="title-main">Bukti Permintaan Barang</div>
        <div class="title-number">{{ $request->request_number }}</div>
        <div class="title-underline"></div>
    </div>

    {{-- Info --}}
    <div class="info-box">
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Unit / Departemen</div>
                <div class="value">{{ $request->unit->name ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Pengaju</div>
                <div class="value">{{ $request->requester->name ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Prioritas</div>
                <div class="value">{{ ucfirst($request->priority) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Status</div>
                <div class="value">
                    <span class="status-label {{ $request->status->value }}">{{ $request->status->label() }}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="label">Tanggal Pengajuan</div>
                <div class="value">{{ $request->request_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Tanggal Dibutuhkan</div>
                <div class="value">{{ $request->needed_date?->format('d/m/Y') ?? '—' }}</div>
            </div>
            @if($request->purpose)
            <div class="info-item" style="flex-basis:100%">
                <div class="label">Keperluan</div>
                <div class="value value-light">{{ $request->purpose }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="section-title">Daftar Barang</div>
    <table>
        <thead>
            <tr>
                <th style="width:24px" class="text-center">No</th>
                <th>Nama Barang</th>
                <th style="width:52px">Kode</th>
                <th style="width:44px" class="text-center">Jumlah</th>
                <th style="width:40px">Satuan</th>
                <th style="width:44px" class="text-center">Disetujui</th>
                <th style="width:44px" class="text-center">Diberikan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($request->items as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                    <span class="fw-bold">{{ $item->item->name }}</span>
                    @if($item->rejection_reason)
                        <div style="font-size:8px;color:#dc2626;margin-top:1px">
                            {{ $item->rejection_reason }}
                        </div>
                    @endif
                </td>
                <td>{{ $item->item->code }}</td>
                <td class="text-center">{{ $item->quantity_requested }}</td>
                <td>{{ $item->item->unit_of_measure }}</td>
                <td class="text-center">{{ $item->quantity_approved ?? '—' }}</td>
                <td class="text-center">{{ $item->quantity_given ?? '—' }}</td>
                <td>
                    @if($item->notes)
                        <span class="text-muted">{{ $item->notes }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                    @if($item->itemStock?->batch_number)
                        <div style="font-size:8px;color:#64748b;margin-top:1px">
                            Batch: {{ $item->itemStock->batch_number }}
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted" style="padding:14px">
                    Tidak ada barang dalam permintaan ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Catatan --}}
    @if($request->notes)
    <div class="notes-box">
        <div class="notes-label">Catatan</div>
        <div class="notes-text">{{ $request->notes }}</div>
    </div>
    @endif

    {{-- Catatan Review / Alasan Penolakan --}}
    @if($request->review_notes)
        <div class="notes-box {{ $request->status->value === 'rejected' ? 'reject' : '' }}">
            <div class="notes-label">
                {{ $request->status->value === 'rejected' ? 'Alasan Penolakan' : 'Catatan Review' }}
            </div>
            <div class="notes-text">{{ $request->review_notes }}</div>
        </div>
    @endif

    {{-- Pengesahan --}}
    <div class="signature-section">
        <div class="section-title">Pengesahan</div>
        <table style="width:100%;border-collapse:collapse;table-layout:fixed">
            <thead>
                <tr>
                    <th style="width:25%;border:none;text-align:center;font-size:8px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-weight:bold;padding:0 6px 4px">Pengaju</th>
                    <th style="width:25%;border:none;text-align:center;font-size:8px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-weight:bold;padding:0 6px 4px">Mengetahui /<br>Menyetujui</th>
                    <th style="width:25%;border:none;text-align:center;font-size:8px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-weight:bold;padding:0 6px 4px">Menyerahkan</th>
                    <th style="width:25%;border:none;text-align:center;font-size:8px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-weight:bold;padding:0 6px 4px">Menerima</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:none;height:44px"></td>
                    <td style="border:none;height:44px"></td>
                    <td style="border:none;height:44px"></td>
                    <td style="border:none;height:44px"></td>
                </tr>
                <tr>
                    <td style="border:none;text-align:center;padding:0 10px">
                        <div style="border-top:1.5px solid #334155;margin:0 2px"></div>
                    </td>
                    <td style="border:none;text-align:center;padding:0 10px">
                        <div style="border-top:1.5px solid #334155;margin:0 2px"></div>
                    </td>
                    <td style="border:none;text-align:center;padding:0 10px">
                        <div style="border-top:1.5px solid #334155;margin:0 2px"></div>
                    </td>
                    <td style="border:none;text-align:center;padding:0 10px">
                        <div style="border-top:1.5px solid #334155;margin:0 2px"></div>
                    </td>
                </tr>
                <tr>
                    <td style="border:none;text-align:center;padding:4px 6px 0;font-size:9px;font-weight:bold;color:#1e293b">{{ $request->requester->name ?? '—' }}</td>
                    <td style="border:none;text-align:center;padding:4px 6px 0;font-size:9px;font-weight:bold;color:#1e293b">{{ $request->reviewer->name ?? '—' }}</td>
                    <td style="border:none;text-align:center;padding:4px 6px 0;font-size:9px;font-weight:bold;color:#1e293b">{{ $request->server->name ?? '—' }}</td>
                    <td style="border:none;text-align:center;padding:4px 6px 0;font-size:9px;font-weight:bold;color:#1e293b">{{ $request->received_by_name ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="border:none;text-align:center;padding:1px 6px 0;font-size:8px;color:#64748b">Petugas Unit</td>
                    <td style="border:none;text-align:center;padding:1px 6px 0;font-size:8px;color:#64748b">Sarpras</td>
                    <td style="border:none;text-align:center;padding:1px 6px 0;font-size:8px;color:#64748b">Petugas Sarpras</td>
                    <td style="border:none;text-align:center;padding:1px 6px 0;font-size:8px;color:#64748b">Petugas Unit</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-footer">
        <span>Dokumen resmi — Dicetak {{ now()->translatedFormat('l, d F Y') }} pukul {{ now()->format('H:i') }} WIB</span>
        <span>Sistem Manajemen Sarpras</span>
    </div>

</body>
</html>
