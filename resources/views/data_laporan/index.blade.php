@extends('layouts.app')
@section('title', 'Data Laporan')
@section('page-title', 'Data Laporan')
@section('content')

<style>
    /* Badge & garis kiri untuk baris Realisasi */
    .badge-realisasi {
        font-size: 12px; font-weight: 600; color: #7a6900;
        display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
    }
    .row-realisasi td:first-child { border-left: 3px solid #9A8600; }

    /* Badge & garis kiri untuk baris Perpanjangan */
    .badge-perpanjangan {
        font-size: 12px; font-weight: 600; color: #641e1e;
        display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
    }
    .row-perpanjangan td:first-child { border-left: 3px solid #641e1e; }

    /* Efek hover pada baris tabel */
    .tbl-row:hover td { background: rgba(152,4,4,0.02); }

    /* Gaya dropdown filter bulan & tahun */
    .filter-select {
        padding: 7px 11px; border: 1.5px solid rgba(0,0,0,0.12); border-radius: 8px;
        font-size: 12px; font-family: 'Montserrat', sans-serif; font-weight: 600;
        color: #374151; background: #fafafa; outline: none; cursor: pointer;
        transition: border-color 0.2s;
    }
    .filter-select:focus { border-color: #980404; }

    /* Kontainer pill filter jenis */
    .jenis-pills { display: flex; gap: 6px; }

    /* Gaya dasar setiap pill */
    .jenis-pill {
        padding: 6px 13px; border-radius: 20px; font-size: 11px; font-weight: 700;
        font-family: 'Montserrat', sans-serif; cursor: pointer; border: 1.5px solid transparent;
        transition: all 0.18s; display: inline-flex; align-items: center; gap: 5px;
    }

    /* Warna per jenis pill */
    .jenis-pill.all          { background: rgba(152,4,4,0.08);   color: #980404; border-color: rgba(152,4,4,0.2); }
    .jenis-pill.realisasi    { background: rgba(154,134,0,0.08); color: #7a6900; border-color: rgba(154,134,0,0.2); }
    .jenis-pill.perpanjangan { background: rgba(100,30,30,0.08); color: #641e1e; border-color: rgba(100,30,30,0.2); }

    /* Warna pill saat aktif atau dihover */
    .jenis-pill.all.active,          .jenis-pill.all:hover          { background: #980404; color: #fff; border-color: #980404; }
    .jenis-pill.realisasi.active,    .jenis-pill.realisasi:hover    { background: #9A8600; color: #fff; border-color: #9A8600; }
    .jenis-pill.perpanjangan.active, .jenis-pill.perpanjangan:hover { background: #641e1e; color: #fff; border-color: #641e1e; }

    /* Tombol aksi (edit & hapus) di kolom tabel */
    .btn-aksi {
        width: 28px; height: 28px; border-radius: 6px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; text-decoration: none; border: none; cursor: pointer;
        transition: opacity 0.15s;
    }
    .btn-aksi:hover { opacity: 0.75; }

    /* Elemen print disembunyikan saat tampilan biasa */
    .print-header, .print-title, .print-footer, .print-ttd { display: none; }

    /* Gaya khusus saat mode cetak (print) */
    @media print {
        @page { size: A4 landscape; margin: 10mm; }

        /* Sembunyikan elemen yang tidak perlu dicetak */
        .no-print, .sidebar, .topbar { display: none !important; }
        body   { background: #fff !important; }
        .layout { display: block !important; }
        .content { padding: 0 !important; overflow: visible !important; }
        .card {
            box-shadow: none !important; border: none !important;
            padding: 0 !important; background: #fff !important;
            backdrop-filter: none !important; -webkit-backdrop-filter: none !important;
        }
        .tabel-wrapper { overflow: visible !important; }

        /* Tampilkan elemen print */
        .print-header  { display: flex !important; align-items: center; padding-bottom: 8px; margin-bottom: 6px; }
        .print-header img { height: 44px; object-fit: contain; }
        .print-title   { display: block !important; font-family: Arial, sans-serif; font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 20px; }
        .print-footer  { display: flex !important; position: fixed; bottom: 0; left: 0; width: 100%; height: 9px; }
        .print-footer .bar-red  { background: #980404; flex: 0 0 92%; height: 100%; }
        .print-footer .bar-gold { background: #F5A800; flex: 1; height: 100%; }
        .print-ttd { display: block !important; }

        /* Sembunyikan kolom Aksi saat cetak */
        th:last-child, td:last-child { display: none !important; }

        /* Gaya tabel saat dicetak */
        table { font-size: 9px !important; width: 100% !important; font-family: Arial, sans-serif !important; border-collapse: collapse !important; }
        th { font-size: 9px !important; font-weight: 700 !important; color: #000 !important; background: none !important; border-bottom: 1.5px solid #000 !important; padding: 5px 6px !important; }
        td { font-size: 9px !important; color: #000 !important; background: none !important; border: none !important; border-bottom: 1px solid #eee !important; padding: 5px 6px !important; }
        .badge-realisasi, .badge-perpanjangan { font-size: 9px !important; color: #000 !important; }
        .row-realisasi td:first-child, .row-perpanjangan td:first-child { border-left: none !important; }
        .tbl-row:hover td { background: none !important; }
    }

    /* Animasi toast notifikasi masuk & keluar */
    @keyframes toastIn  { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes toastOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-8px); } }
</style>

{{-- Toast notifikasi sukses --}}
@if(session('success'))
<div id="successToast" style="position:fixed;top:20px;right:20px;z-index:9999;background:#fff;border-left:3px solid #059669;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.10);padding:12px 16px;min-width:280px;max-width:360px;display:flex;align-items:center;gap:10px;animation:toastIn .3s ease;">
    <i class="fas fa-check-circle" style="color:#059669;font-size:16px;flex-shrink:0;"></i>
    <span style="font-family:'Montserrat',sans-serif;font-size:12px;color:#374151;flex:1;">{{ session('success') }}</span>
    <button onclick="closeToast()" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:14px;padding:0;line-height:1;">
        <i class="fas fa-times"></i>
    </button>
</div>
<script>
// Tutup toast secara manual
function closeToast() {
    const t = document.getElementById('successToast');
    if (t) { t.style.animation = 'toastOut .25s ease'; setTimeout(() => t.remove(), 250); }
}
// Auto-tutup toast setelah 4,5 detik
setTimeout(closeToast, 4500);
</script>
@endif

{{-- Elemen header untuk tampilan cetak --}}
<div class="print-header">
    <img src="{{ asset('assets/img/logo_brksyariah.png') }}" alt="Logo BRK Syariah">
</div>

{{-- Judul laporan saat dicetak --}}
<div class="print-title">
    Laporan Harian Realisasi Rahn<br>
    Periode : {{ $namaBulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? '-' }} {{ $tahun }}
</div>

{{-- Garis footer merah-emas di bagian bawah halaman cetak --}}
<div class="print-footer">
    <div class="bar-red"></div>
    <div class="bar-gold"></div>
</div>

{{-- Tanda tangan pejabat di halaman cetak --}}
<div class="print-ttd">
    <div style="position:fixed;bottom:20px;right:20px;display:flex;gap:60px;">
        <div style="text-align:center;">
            <div style="margin-bottom:60px;font-size:11px;">Menyetujui,</div>
            <div style="border-top:1px solid #000;padding-top:6px;width:160px;">
                <div style="font-weight:700;font-size:11px;">Edi Irawan</div>
                <div style="font-size:10px;color:#444;">Pimpinan Bagian Operasional</div>
            </div>
        </div>
        <div style="text-align:center;">
            <div style="margin-bottom:60px;font-size:11px;">Mengetahui,</div>
            <div style="border-top:1px solid #000;padding-top:6px;width:160px;">
                <div style="font-weight:700;font-size:11px;">Sri Handayani</div>
                <div style="font-size:10px;color:#444;">Administrasi Komersial dan Rahn</div>
            </div>
        </div>
    </div>
</div>

{{-- Bagian filter: bulan, tahun, dan jenis laporan --}}
@php $activeJenis = request('jenis', 'semua'); @endphp
<div class="card no-print" style="padding:14px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('data-laporan.index') }}" id="formFilter"
          style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">

        {{-- Hidden input untuk menyimpan nilai filter jenis --}}
        <input type="hidden" name="jenis" id="inputJenis" value="{{ $activeJenis }}">

        <span style="font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;color:#7a5d4a;">
            <i class="fas fa-filter" style="color:#980404;margin-right:4px;"></i>Filter:
        </span>

        {{-- Dropdown pilih bulan --}}
        <select name="bulan" class="filter-select">
            @foreach($namaBulan as $nilai => $label)
                <option value="{{ $nilai }}" {{ $bulan == $nilai ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        {{-- Dropdown pilih tahun --}}
        <select name="tahun" class="filter-select">
            @foreach($daftarTahun as $thn)
                <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
            @endforeach
        </select>

        {{-- Pill filter jenis: Semua, Realisasi, Perpanjangan --}}
        <div class="jenis-pills">
            <span class="jenis-pill all {{ $activeJenis === 'semua' ? 'active' : '' }}" onclick="setJenis('semua')">
                <i class="fas fa-layer-group"></i> Semua
            </span>
            <span class="jenis-pill realisasi {{ $activeJenis === 'realisasi' ? 'active' : '' }}" onclick="setJenis('realisasi')">
                <i class="fas fa-file-alt"></i> Realisasi
            </span>
            <span class="jenis-pill perpanjangan {{ $activeJenis === 'perpanjangan' ? 'active' : '' }}" onclick="setJenis('perpanjangan')">
                <i class="fas fa-redo"></i> Perpanjangan
            </span>
        </div>

        {{-- Tombol submit filter --}}
        <button type="submit"
            style="padding:7px 16px;background:#980404;color:white;border:none;border-radius:8px;font-size:12px;font-family:'Montserrat',sans-serif;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:background .2s;"
            onmouseover="this.style.background='#7a0303'" onmouseout="this.style.background='#980404'">
            <i class="fas fa-search"></i> Tampilkan
        </button>

        {{-- Tombol reset filter --}}
        <a href="{{ route('data-laporan.index') }}"
            style="padding:7px 13px;background:#f3f4f6;color:#6b7280;text-decoration:none;border-radius:8px;font-family:'Montserrat',sans-serif;font-weight:600;font-size:12px;display:inline-flex;align-items:center;gap:6px;transition:background .2s;"
            onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
            <i class="fas fa-times"></i> Reset
        </a>

        {{-- Informasi jumlah data yang ditampilkan --}}
        <div style="margin-left:auto;">
            <span style="font-family:'Montserrat',sans-serif;font-size:11px;color:#9d8875;">
                <strong style="color:#980404;">{{ $laporanGabungan->count() }}</strong> data
                — {{ $namaBulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? '-' }} {{ $tahun }}
            </span>
        </div>
    </form>
</div>

{{-- Tabel data laporan --}}
@php
$tdStyle = 'padding:11px 12px;border-bottom:1px solid rgba(0,0,0,0.05);color:#374151;white-space:nowrap;';
$thStyle = 'padding:13px 12px;text-align:left;color:#980404;font-family:\'Montserrat\',sans-serif;font-weight:600;border-bottom:2px solid rgba(152,4,4,0.15);white-space:nowrap;';
@endphp
<div class="card">
    <div class="tabel-wrapper" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:12px;">

            {{-- Kepala tabel --}}
            <thead style="background:linear-gradient(90deg,rgba(152,4,4,0.06),rgba(152,4,4,0.02));">
                <tr>
                    <th style="{{ $thStyle }}">No</th>
                    <th style="{{ $thStyle }}">Jenis</th>
                    <th style="{{ $thStyle }}">No. Akad</th>
                    <th style="{{ $thStyle }}">No. Loan</th>
                    <th style="{{ $thStyle }}">Nama Debitur</th>
                    <th style="{{ $thStyle }}">Berat (g)</th>
                    <th style="{{ $thStyle }}">Kadar</th>
                    <th style="{{ $thStyle }}">Taksiran</th>
                    <th style="{{ $thStyle }}">Pembiayaan</th>
                    <th style="{{ $thStyle }}">Pendapatan / Biaya Sewa</th>
                    <th style="{{ $thStyle }}">Tgl Realisasi</th>
                    <th style="{{ $thStyle }}">Tgl Jatuh Tempo</th>
                    <th style="{{ $thStyle }} no-print">Aksi</th>
                </tr>
            </thead>

            <tbody>
                {{-- Looping data laporan gabungan (realisasi + perpanjangan) --}}
                @forelse($laporanGabungan as $index => $item)
                @php $r = $item['jenis'] === 'realisasi'; @endphp
                <tr class="tbl-row {{ $r ? 'row-realisasi' : 'row-perpanjangan' }}">

                    {{-- Nomor urut --}}
                    <td style="{{ $tdStyle }}color:#9d8875;">{{ $index + 1 }}</td>

                    {{-- Badge jenis: Realisasi atau Perpanjangan --}}
                    <td style="{{ $tdStyle }}">
                        <span class="{{ $r ? 'badge-realisasi' : 'badge-perpanjangan' }}">
                            {{ $r ? 'Realisasi' : 'Perpanjangan' }}
                        </span>
                    </td>

                    <td style="{{ $tdStyle }}font-weight:600;">{{ $item['no_akad'] }}</td>
                    <td style="{{ $tdStyle }}">{{ $item['no_loan'] }}</td>
                    <td style="{{ $tdStyle }}white-space:normal;">{{ $item['nama_debitur'] }}</td>

                    {{-- Berat & kadar emas --}}
                    <td style="{{ $tdStyle }}">{{ isset($item['berat']) ? number_format($item['berat'], 2) : '—' }}</td>
                    <td style="{{ $tdStyle }}">{{ isset($item['kadar']) ? (int)$item['kadar'] . 'K' : '—' }}</td>

                    {{-- Nilai taksiran & pembiayaan --}}
                    <td style="{{ $tdStyle }}">{{ isset($item['taksiran'])   ? 'Rp ' . number_format($item['taksiran'],   0, ',', '.') : '—' }}</td>
                    <td style="{{ $tdStyle }}">{{ isset($item['pembiayaan']) ? 'Rp ' . number_format($item['pembiayaan'], 0, ',', '.') : '—' }}</td>

                    {{-- Pendapatan sewa (realisasi) atau biaya sewa tambahan (perpanjangan) --}}
                    <td style="{{ $tdStyle }}">
                        @if($r && isset($item['pendapatan_sewa']))
                            Rp {{ number_format($item['pendapatan_sewa'], 0, ',', '.') }}
                        @elseif(!$r && isset($item['biaya_sewa_tambahan']))
                            Rp {{ number_format($item['biaya_sewa_tambahan'], 0, ',', '.') }}
                        @else —
                        @endif
                    </td>

                    {{-- Tanggal realisasi & jatuh tempo --}}
                    <td style="{{ $tdStyle }}">{{ !empty($item['tanggal_realisasi']) ? \Carbon\Carbon::parse($item['tanggal_realisasi'])->format('d/m/Y') : '—' }}</td>
                    <td style="{{ $tdStyle }}">{{ !empty($item['jatuh_tempo'])       ? \Carbon\Carbon::parse($item['jatuh_tempo'])->format('d/m/Y')       : '—' }}</td>

                    {{-- Tombol edit & hapus (disembunyikan saat cetak) --}}
                    <td style="{{ $tdStyle }}" class="no-print">
                        <div style="display:flex;gap:4px;">
                            {{-- Tombol edit --}}
                            <a href="{{ route($r ? 'laporan-realisasi.edit' : 'laporan-perpanjangan.edit', $item['id']) }}"
                               class="btn-aksi" style="background:rgba(29,78,216,0.08);color:#1d4ed8;" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>

                            {{-- Form hapus dengan konfirmasi --}}
                            <form action="{{ route($r ? 'laporan-realisasi.destroy' : 'laporan-perpanjangan.destroy', $item['id']) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Yakin ingin menghapus data {{ $r ? 'realisasi' : 'perpanjangan' }} ini?')"
                                    class="btn-aksi" style="background:rgba(152,4,4,0.08);color:#980404;" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Tampilkan pesan jika data kosong --}}
                @empty
                <tr>
                    <td colspan="13" style="text-align:center;padding:50px 20px;color:#9d8875;">
                        <i class="fas fa-inbox" style="font-size:48px;color:#d1c5b8;margin-bottom:12px;display:block;"></i>
                        <p style="margin:0;font-size:13px;font-family:'Montserrat',sans-serif;">
                            Tidak ada data untuk <strong>{{ $namaBulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? '-' }} {{ $tahun }}</strong>
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tombol cetak laporan --}}
<div class="no-print" style="display:flex;justify-content:flex-end;margin-top:12px;">
    <button type="button" onclick="cetakLaporan()"
        style="padding:8px 20px;background:#1d4ed8;color:white;border:none;border-radius:8px;font-size:12px;font-family:'Montserrat',sans-serif;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .2s;box-shadow:0 3px 8px rgba(29,78,216,.2);"
        onmouseover="this.style.background='#1a3d99'" onmouseout="this.style.background='#1d4ed8'">
        <i class="fas fa-print"></i>
        Cetak Laporan — {{ $namaBulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? '-' }} {{ $tahun }}
    </button>
</div>

<script>
// Set nilai jenis filter dan tandai pill yang aktif
function setJenis(val) {
    document.getElementById('inputJenis').value = val;
    document.querySelectorAll('.jenis-pill').forEach(el => el.classList.remove('active'));
    document.querySelector('.jenis-pill.' + (val === 'semua' ? 'all' : val))?.classList.add('active');
}

// Ubah judul halaman lalu panggil dialog cetak browser
function cetakLaporan() {
    const bulan = '{{ $namaBulan[str_pad($bulan, "2", "0", STR_PAD_LEFT)] ?? "-" }}';
    const tahun = '{{ $tahun }}';
    document.title = 'Laporan Rahn ' + bulan + ' ' + tahun;
    window.print();
    // Kembalikan judul halaman setelah cetak selesai
    setTimeout(() => { document.title = 'Data Laporan'; }, 2000);
}

// Auto-print jika halaman dibuka dengan parameter ?print=1
if (new URLSearchParams(window.location.search).get('print') === '1') {
    window.addEventListener('load', function() {
        setTimeout(cetakLaporan, 800);
    });
}
</script>

@endsection