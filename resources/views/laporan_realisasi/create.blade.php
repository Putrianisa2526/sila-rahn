@extends('layouts.app')

@section('title', $laporanRealisasi ? 'Edit Laporan Realisasi' : 'Tambah Laporan Realisasi')
@section('page-title', $laporanRealisasi ? 'Edit Laporan Realisasi' : 'Tambah Laporan Realisasi')

@section('content')

{{-- Toast notifikasi sukses simpan / update data --}}
@if (session('success'))
<div id="successToast" style="
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    background: #fff; border-left: 3px solid #059669;
    border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.10);
    padding: 12px 16px; min-width: 280px; max-width: 340px;
    display: flex; align-items: center; gap: 10px;
    animation: toastIn 0.3s ease;
">
    <i class="fas fa-check-circle" style="color: #059669; font-size: 16px; flex-shrink: 0;"></i>
    <span style="font-family: 'Montserrat', sans-serif; font-size: 12px; color: #374151; flex: 1;">
        {{ session('success') }}
    </span>
    <button onclick="closeToast()" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 14px; padding: 0; line-height: 1;">
        <i class="fas fa-times"></i>
    </button>
</div>
<style>
/* Animasi toast masuk & keluar */
@keyframes toastIn  { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes toastOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-8px); } }
</style>
<script>
// Tutup toast secara manual
function closeToast() {
    const t = document.getElementById('successToast');
    if (t) { t.style.animation = 'toastOut 0.25s ease'; setTimeout(() => t.remove(), 250); }
}
// Auto-tutup toast setelah 4,5 detik
setTimeout(closeToast, 4500);

{{-- Reset form hanya pada mode tambah (bukan edit) setelah sukses simpan --}}
@if (session('success') && !$laporanRealisasi)
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('formLaporan').reset();
    hitungPendapatanSewa();
});
@endif
</script>
@endif

{{-- Gaya global form --}}
<style>
    /* Gaya umum input teks */
    .form-input {
        width: 100%; padding: 8px 11px;
        border: 1.5px solid rgba(0,0,0,0.10);
        border-radius: 7px; font-size: 12px;
        font-family: 'Montserrat', sans-serif;
        outline: none; box-sizing: border-box;
        background: #fafafa; color: #374151;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    /* Input saat fokus */
    .form-input:focus {
        border-color: #980404;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(152,4,4,0.07);
    }
    /* Input dengan kondisi error */
    .form-input.is-error { border-color: #dc2626; }

    /* Label field */
    .field-label {
        display: block; margin-bottom: 6px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600; font-size: 12px; color: #980404;
    }
    .field-label span { color: #dc2626; } /* Tanda bintang wajib isi */

    /* Teks pesan error per field */
    .field-error { margin: 3px 0 0; font-size: 11px; color: #dc2626; }
</style>

{{-- Kotak pesan error validasi server --}}
@if ($errors->any())
<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;">
    <p style="margin: 0 0 4px; font-weight: 700; color: #dc2626; font-family: 'Montserrat', sans-serif; font-size: 11px;">
        <i class="fas fa-exclamation-circle"></i> Periksa isian berikut:
    </p>
    <ul style="margin: 0; padding-left: 16px; color: #dc2626; font-size: 11px; line-height: 1.8;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Bar preview No. Akad dan No. Loan (otomatis dari sistem) --}}
<div style="
    display: flex; align-items: stretch; gap: 1px;
    background: rgba(0,0,0,0.06); border-radius: 9px; overflow: hidden;
    margin-bottom: 18px;
">
    <div style="flex: 1; background: #fff; padding: 10px 14px;">
        <div style="font-family: 'Montserrat', sans-serif; font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px;">
            No. Akad {{ $laporanRealisasi ? '' : '(Preview)' }}
        </div>
        <div style="font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 700; color: #980404; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ $previewNoAkad }}
        </div>
    </div>
    <div style="flex: 1; background: #fff; padding: 10px 14px;">
        <div style="font-family: 'Montserrat', sans-serif; font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px;">
            No. Loan {{ $laporanRealisasi ? '' : '(Preview)' }}
        </div>
        <div style="font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 700; color: #980404; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ $previewNoLoan }}
        </div>
    </div>
</div>

{{-- Kartu form utama --}}
<div class="card" style="padding: 22px;">
    {{-- Action form berbeda untuk mode tambah (store) dan edit (update) --}}
    <form
        action="{{ $laporanRealisasi
            ? route('laporan-realisasi.update', $laporanRealisasi->id)
            : route('laporan-realisasi.store') }}"
        method="POST"
        id="formLaporan">
        @csrf
        {{-- Spoofing method PUT untuk mode edit --}}
        @if($laporanRealisasi)
            @method('PUT')
        @endif

        {{-- Input nama debitur --}}
        <div style="margin-bottom: 14px;">
            <label class="field-label">Nama Debitur <span>*</span></label>
            <input type="text" name="nama_debitur"
                value="{{ old('nama_debitur', $laporanRealisasi?->nama_debitur) }}"
                placeholder="Nama lengkap debitur"
                class="form-input {{ $errors->has('nama_debitur') ? 'is-error' : '' }}">
            @error('nama_debitur')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- Input berat emas (gram) & kadar --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
            <div>
                <label class="field-label">Berat (gram) <span>*</span></label>
                {{-- Perubahan berat memicu kalkulasi ulang pendapatan sewa --}}
                <input type="number" step="0.01" name="berat" id="inputBerat"
                    value="{{ old('berat', $laporanRealisasi?->berat) }}"
                    placeholder="cth: 10.50"
                    class="form-input {{ $errors->has('berat') ? 'is-error' : '' }}"
                    oninput="hitungPendapatanSewa()">
                @error('berat')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label">Kadar <span>*</span></label>
                <input type="number" step="0.01" name="kadar"
                    value="{{ old('kadar', $laporanRealisasi?->kadar) }}"
                    placeholder="cth: 24"
                    class="form-input {{ $errors->has('kadar') ? 'is-error' : '' }}">
                @error('kadar')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">

            {{-- Input taksiran: tampilan format Rupiah, hidden input menyimpan angka murni --}}
            <div>
                <label class="field-label">Taksiran (Rp) <span>*</span></label>
                <input type="text"
                    id="inputTaksiran"
                    placeholder="cth: Rp 5.000.000"
                    class="form-input {{ $errors->has('taksiran') ? 'is-error' : '' }}"
                    oninput="formatRupiahInput(this, 'hiddenTaksiran')">
                <input type="hidden"
                    name="taksiran"
                    id="hiddenTaksiran"
                    value="{{ old('taksiran', $laporanRealisasi?->taksiran) }}">
                @error('taksiran')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input pembiayaan: tampilan format Rupiah, hidden input menyimpan angka murni --}}
            <div>
                <label class="field-label">Pembiayaan (Rp) <span>*</span></label>
                <input type="text"
                    id="inputPembiayaan"
                    placeholder="cth: Rp 4.000.000"
                    class="form-input {{ $errors->has('pembiayaan') ? 'is-error' : '' }}"
                    oninput="formatRupiahInput(this, 'hiddenPembiayaan')">
                <input type="hidden"
                    name="pembiayaan"
                    id="hiddenPembiayaan"
                    value="{{ old('pembiayaan', $laporanRealisasi?->pembiayaan) }}">
                @error('pembiayaan')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Input tanggal realisasi & tanggal jatuh tempo --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
            <div>
                <label class="field-label">Tanggal Realisasi <span>*</span></label>
                {{-- Perubahan tanggal memicu kalkulasi ulang pendapatan sewa --}}
                <input type="date" name="tanggal_realisasi" id="inputTglRealisasi"
                    value="{{ old('tanggal_realisasi',
                        $laporanRealisasi
                            ? \Carbon\Carbon::parse($laporanRealisasi->tanggal_realisasi)->format('Y-m-d')
                            : date('Y-m-d')) }}"
                    class="form-input {{ $errors->has('tanggal_realisasi') ? 'is-error' : '' }}"
                    onchange="hitungPendapatanSewa()">
                @error('tanggal_realisasi')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label">Tanggal Jatuh Tempo <span>*</span></label>
                <input type="date" name="tanggal_jatuh_tempo" id="inputTglJatuhTempo"
                    value="{{ old('tanggal_jatuh_tempo',
                        $laporanRealisasi
                            ? \Carbon\Carbon::parse($laporanRealisasi->tanggal_jatuh_tempo)->format('Y-m-d')
                            : '') }}"
                    class="form-input {{ $errors->has('tanggal_jatuh_tempo') ? 'is-error' : '' }}"
                    onchange="hitungPendapatanSewa()">
                @error('tanggal_jatuh_tempo')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Preview kalkulasi pendapatan sewa (berat × tarif × jumlah bulan) --}}
        <div style="
            display: flex; align-items: center; justify-content: space-between;
            background: #fafafa; border: 1px dashed rgba(152,4,4,0.2);
            border-radius: 7px; padding: 10px 14px; margin-bottom: 22px;
        ">
            <div style="display: flex; align-items: center; gap: 7px;">
                <i class="fas fa-calculator" style="color: #980404; font-size: 11px;"></i>
                <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px;">Pendapatan Sewa</span>
            </div>
            <div style="text-align: right;">
                <span style="font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #980404;" id="previewPendapatan">Rp 0</span>
            </div>
        </div>

        {{-- Tombol aksi: Simpan/Update, Batal (mode tambah saja), dan Kembali --}}
        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="submit" style="
                padding: 8px 20px; background: #980404; color: #fff;
                border: none; cursor: pointer; border-radius: 7px;
                font-family: 'Montserrat', sans-serif; font-weight: 700;
                font-size: 12px; display: inline-flex; align-items: center; gap: 6px;
                box-shadow: 0 3px 8px rgba(152,4,4,0.22); transition: background 0.2s;"
                onmouseover="this.style.background='#7a0303'"
                onmouseout="this.style.background='#980404'">
                <i class="fas fa-save"></i> {{ $laporanRealisasi ? 'Update' : 'Simpan' }}
            </button>

            {{-- Tombol Batal hanya muncul pada mode tambah data baru --}}
            @if(!$laporanRealisasi)
            <button type="button" onclick="konfirmasiBatal()" style="
                padding: 8px 16px; background: transparent; color: #dc2626;
                border: 1.5px solid rgba(220,38,38,0.25); border-radius: 7px;
                font-family: 'Montserrat', sans-serif; font-weight: 600;
                font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
                transition: background 0.2s, border-color 0.2s;"
                onmouseover="this.style.background='rgba(220,38,38,0.05)'; this.style.borderColor='rgba(220,38,38,0.5)'"
                onmouseout="this.style.background='transparent'; this.style.borderColor='rgba(220,38,38,0.25)'">
                <i class="fas fa-times" style="font-size: 11px;"></i> Batal
            </button>
            @endif

            {{-- Tautan kembali ke daftar laporan realisasi --}}
            <a href="{{ route('laporan-realisasi.index') }}" style="
                padding: 8px 16px; background: transparent; color: #6b7280;
                border: 1.5px solid rgba(0,0,0,0.10); border-radius: 7px;
                font-family: 'Montserrat', sans-serif; font-weight: 600;
                font-size: 12px; text-decoration: none; display: inline-flex;
                align-items: center; gap: 6px; transition: color 0.2s, border-color 0.2s;"
                onmouseover="this.style.color='#374151'; this.style.borderColor='rgba(0,0,0,0.25)'"
                onmouseout="this.style.color='#6b7280'; this.style.borderColor='rgba(0,0,0,0.10)'">
                <i class="fas fa-arrow-left" style="font-size: 11px;"></i> Kembali
            </a>
        </div>

    </form>
</div>

<script>
// Hitung preview pendapatan sewa: tarif × berat × selisih bulan (realisasi ke jatuh tempo)
function hitungPendapatanSewa() {
    const berat         = parseFloat(document.getElementById('inputBerat').value) || 0;
    const tglRealisasi  = document.getElementById('inputTglRealisasi').value;
    const tglJatuhTempo = document.getElementById('inputTglJatuhTempo').value;

    let jumlahBulan = 0;
    if (tglRealisasi && tglJatuhTempo) {
        const s = new Date(tglRealisasi);
        const e = new Date(tglJatuhTempo);
        jumlahBulan = (e.getFullYear() - s.getFullYear()) * 12 + (e.getMonth() - s.getMonth());
        // Minimal 1 bulan
        if (jumlahBulan < 1) jumlahBulan = 1;
    }

    const tarifUjrah = {{ $tarifUjrah }};
    const pendapatan = tarifUjrah * berat * jumlahBulan;

    document.getElementById('previewPendapatan').textContent =
        'Rp ' + (berat > 0 && jumlahBulan > 0 ? pendapatan.toLocaleString('id-ID') : '0');
}

// Konfirmasi reset form saat klik tombol Batal
function konfirmasiBatal() {
    if (confirm('Reset semua isian form?')) {
        document.getElementById('formLaporan').reset();
        hitungPendapatanSewa();
    }
}

// Listener perubahan input yang mempengaruhi kalkulasi pendapatan sewa
document.getElementById('inputBerat').addEventListener('input',          hitungPendapatanSewa);
document.getElementById('inputTglRealisasi').addEventListener('change',  hitungPendapatanSewa);
document.getElementById('inputTglJatuhTempo').addEventListener('change', hitungPendapatanSewa);

// Hitung preview saat halaman pertama kali dimuat (penting untuk mode edit)
hitungPendapatanSewa();

// Format input Rupiah: tampilkan teks "Rp x.xxx" dan simpan angka murni ke hidden input
function formatRupiahInput(input, hiddenId) {
    let angka = input.value.replace(/\D/g, '');
    document.getElementById(hiddenId).value = angka;
    input.value = angka ? 'Rp ' + Number(angka).toLocaleString('id-ID') : '';
}

// Auto-format nilai taksiran & pembiayaan saat mode edit (data sudah terisi dari DB)
document.addEventListener('DOMContentLoaded', function () {
    const taksiranVal   = document.getElementById('hiddenTaksiran')?.value;
    const pembiayaanVal = document.getElementById('hiddenPembiayaan')?.value;

    if (taksiranVal) {
        document.getElementById('inputTaksiran').value =
            'Rp ' + Number(taksiranVal).toLocaleString('id-ID');
    }
    if (pembiayaanVal) {
        document.getElementById('inputPembiayaan').value =
            'Rp ' + Number(pembiayaanVal).toLocaleString('id-ID');
    }
});
</script>

@endsection