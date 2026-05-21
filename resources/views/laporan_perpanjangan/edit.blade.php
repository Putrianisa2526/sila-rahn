@extends('layouts.app')

@section('title', 'Edit Laporan Perpanjangan')
@section('page-title', 'Edit Laporan Perpanjangan')

@section('content')

@if (session('success'))
<div id="successToast" style="
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    background: #fff; border-left: 3px solid #059669;
    border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.10);
    padding: 12px 16px; min-width: 280px; max-width: 340px;
    display: flex; align-items: center; gap: 10px;
    animation: toastIn 0.3s ease;">
    <i class="fas fa-check-circle" style="color:#059669;font-size:16px;flex-shrink:0;"></i>
    <span style="font-family:'Montserrat',sans-serif;font-size:12px;color:#374151;flex:1;">{{ session('success') }}</span>
    <button onclick="closeToast()" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:14px;padding:0;">
        <i class="fas fa-times"></i>
    </button>
</div>
<style>
@keyframes toastIn  { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
@keyframes toastOut { from { opacity:1; transform:translateY(0); } to { opacity:0; transform:translateY(-8px); } }
</style>
<script>
function closeToast() {
    const t = document.getElementById('successToast');
    if (t) { t.style.animation = 'toastOut 0.25s ease'; setTimeout(() => t.remove(), 250); }
}
setTimeout(closeToast, 4500);
</script>
@endif

<style>
    .form-input {
        width: 100%; padding: 6px 10px;
        border: 1.5px solid rgba(0,0,0,0.10);
        border-radius: 7px; font-size: 12px;
        font-family: 'Montserrat', sans-serif;
        outline: none; box-sizing: border-box;
        background: #fafafa; color: #374151;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus {
        border-color: #980404; background: #fff;
        box-shadow: 0 0 0 3px rgba(152,4,4,0.07);
    }
    .form-input.is-error { border-color: #dc2626; }
    .field-label {
        display: block; margin-bottom: 3px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600; font-size: 12px; color: #980404;
    }
    .field-label span { color: #dc2626; }
    .field-error { margin: 2px 0 0; font-size: 11px; color: #dc2626; }
    .ref-box {
        width: 100%; padding: 6px 10px;
        border: 1.5px solid rgba(0,0,0,0.08); border-radius: 8px;
        font-size: 12px; font-family: 'Montserrat', sans-serif;
        background: #f3f4f6; color: #374151; font-weight: 600;
        box-sizing: border-box; min-height: 30px;
        display: flex; align-items: center;
    }
    .section-divider {
        font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700;
        color: #9ca3af; text-transform: uppercase; letter-spacing: 0.6px;
        margin: 5px 0; padding-bottom: 6px;
        border-bottom: 1px solid rgba(0,0,0,0.07);
    }
</style>

@if ($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;margin-bottom:16px;">
    <p style="margin:0 0 4px;font-weight:700;color:#dc2626;font-family:'Montserrat',sans-serif;font-size:11px;">
        <i class="fas fa-exclamation-circle"></i> Periksa isian berikut:
    </p>
    <ul style="margin:0;padding-left:16px;color:#dc2626;font-size:11px;line-height:1.8;">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

{{-- META BAR --}}
<div style="
    display:flex; align-items:stretch; gap:1px;
    background:rgba(0,0,0,0.06); border-radius:9px; overflow:hidden;
    margin-bottom:18px;">
    <div style="flex:1;background:#fff;padding:10px 14px;">
        <div style="font-family:'Montserrat',sans-serif;font-size:9px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.6px;margin-bottom:3px;">No. Akad</div>
        <div style="font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;color:#980404;">{{ $perpanjangan->no_akad }}</div>
    </div>
    <div style="flex:1;background:#fff;padding:10px 14px;">
        <div style="font-family:'Montserrat',sans-serif;font-size:9px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.6px;margin-bottom:3px;">No. Loan</div>
        <div style="font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;color:#980404;">{{ $perpanjangan->no_loan }}</div>
    </div>
</div>

<div class="card" style="padding:24px;">
    <form action="{{ route('laporan-perpanjangan.update', $perpanjangan->id) }}" method="POST" id="formEdit">
        @csrf @method('PUT')

        {{-- Data Realisasi Asal (readonly) --}}
        <div class="section-divider">Data Realisasi Asal</div>

        <div style="margin-bottom:16px;">
            <label class="field-label">Nama Debitur <span>*</span></label>
            <input type="text" name="nama_debitur"
                value="{{ old('nama_debitur', $perpanjangan->nama_debitur) }}"
                class="form-input {{ $errors->has('nama_debitur') ? 'is-error' : '' }}">
            @error('nama_debitur')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div>
                <label class="field-label">Berat (gram)</label>
                <div class="ref-box">{{ number_format($perpanjangan->berat_ref, 2) }} gram</div>
            </div>
            <div>
                <label class="field-label">Biaya Sewa Tambahan</label>
                <div class="ref-box" id="refBiaya">
                    Rp {{ number_format($perpanjangan->biaya_sewa_tambahan, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Data Perpanjangan --}}
        <div class="section-divider">Data Perpanjangan</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div>
                <label class="field-label">Tanggal Perpanjangan <span>*</span></label>
                <input type="date" name="tanggal_perpanjangan" id="inputTglPerpanjangan"
                    value="{{ old('tanggal_perpanjangan', \Carbon\Carbon::parse($perpanjangan->tanggal_perpanjangan)->format('Y-m-d')) }}"
                    class="form-input {{ $errors->has('tanggal_perpanjangan') ? 'is-error' : '' }}"
                    onchange="hitungBiaya()">
                @error('tanggal_perpanjangan')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label">Jumlah Bulan <span>*</span></label>
                <input type="number" name="jumlah_bulan" id="inputJumlahBulan"
                    value="{{ old('jumlah_bulan', $perpanjangan->jumlah_bulan) }}"
                    min="1" placeholder="cth: 3"
                    class="form-input {{ $errors->has('jumlah_bulan') ? 'is-error' : '' }}"
                    oninput="hitungBiaya()">
                @error('jumlah_bulan')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label class="field-label">Tanggal Jatuh Tempo Baru</label>
            <div class="ref-box" id="refJatuhTempoBaru">
                {{ \Carbon\Carbon::parse($perpanjangan->tanggal_jatuh_tempo_baru)->format('d/m/Y') }}
            </div>
            <input type="hidden" name="tanggal_jatuh_tempo_baru" id="hiddenJatuhTempoBaru"
                value="{{ \Carbon\Carbon::parse($perpanjangan->tanggal_jatuh_tempo_baru)->format('Y-m-d') }}">
        </div>

        {{-- Preview Biaya --}}
        <div style="
            display:flex; align-items:center; justify-content:space-between;
            background:#fafafa; border:1px dashed rgba(152,4,4,0.2);
            border-radius:7px; padding:10px 14px; margin-bottom:28px;">
            <div style="display:flex;align-items:center;gap:7px;">
                <i class="fas fa-calculator" style="color:#980404;font-size:11px;"></i>
                <span style="font-family:'Montserrat',sans-serif;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.4px;">Tambahan Biaya Sewa</span>
            </div>
            <span style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:700;color:#980404;" id="previewBiaya">
                Rp {{ number_format($perpanjangan->biaya_sewa_tambahan, 0, ',', '.') }}
            </span>
        </div>

        <input type="hidden" name="biaya_sewa_tambahan" id="hiddenBiaya"
            value="{{ $perpanjangan->biaya_sewa_tambahan }}">

        {{-- Actions --}}
        <div style="display:flex;align-items:center;gap:8px;">
            <button type="submit" style="
                padding:8px 20px;background:#980404;color:#fff;
                border:none;cursor:pointer;border-radius:7px;
                font-family:'Montserrat',sans-serif;font-weight:700;font-size:12px;
                display:inline-flex;align-items:center;gap:6px;
                box-shadow:0 3px 8px rgba(152,4,4,0.22);transition:background 0.2s;"
                onmouseover="this.style.background='#7a0303'"
                onmouseout="this.style.background='#980404'">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('data-laporan.index') }}" style="
                padding:8px 16px;background:transparent;color:#6b7280;
                border:1.5px solid rgba(0,0,0,0.10);border-radius:7px;
                font-family:'Montserrat',sans-serif;font-weight:600;font-size:12px;
                text-decoration:none;display:inline-flex;align-items:center;gap:6px;
                transition:color 0.2s,border-color 0.2s;"
                onmouseover="this.style.color='#374151';this.style.borderColor='rgba(0,0,0,0.25)'"
                onmouseout="this.style.color='#6b7280';this.style.borderColor='rgba(0,0,0,0.10)'">
                <i class="fas fa-arrow-left" style="font-size:11px;"></i> Kembali
            </a>
        </div>

    </form>
</div>

<script>
const beratRef   = {{ $perpanjangan->berat_ref }};
const tarifUjrah = {{ $tarifUjrah }};

function tambahBulan(isoDate, n) {
    const [y, m, d] = isoDate.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    dt.setMonth(dt.getMonth() + n);
    return dt.getFullYear()
        + '-' + String(dt.getMonth() + 1).padStart(2, '0')
        + '-' + String(dt.getDate()).padStart(2, '0');
}

function isoKeTampilan(iso) {
    if (!iso) return '—';
    const [y, m, d] = iso.split('-');
    return d + '/' + m + '/' + y;
}

function hitungBiaya() {
    const tgl   = document.getElementById('inputTglPerpanjangan').value;
    const bulan = parseInt(document.getElementById('inputJumlahBulan').value) || 0;

    if (tgl && bulan > 0) {
        const jatuhTempoBaru = tambahBulan(tgl, bulan);
        document.getElementById('refJatuhTempoBaru').textContent  = isoKeTampilan(jatuhTempoBaru);
        document.getElementById('hiddenJatuhTempoBaru').value     = jatuhTempoBaru;
    }

    const biaya = beratRef * tarifUjrah * bulan;
    document.getElementById('previewBiaya').textContent  = 'Rp ' + (biaya > 0 ? biaya.toLocaleString('id-ID') : '0');
    document.getElementById('hiddenBiaya').value         = biaya;
}

// Hitung saat load
hitungBiaya();
</script>

@endsection