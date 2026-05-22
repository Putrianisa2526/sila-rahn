@extends('layouts.app')

@section('title', 'Tambah Laporan Perpanjangan')
@section('page-title', 'Tambah Laporan Perpanjangan')

@section('content')

{{-- Toast notifikasi sukses simpan data --}}
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

// Reset form setelah sukses simpan
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('formPerpanjangan').reset();
    resetRefBoxes();
});
</script>
@endif

<style>
    /* Gaya umum input teks */
    .form-input {
        width: 100%; padding: 6px 10px;
        border: 1.5px solid rgba(0,0,0,0.10);
        border-radius: 7px; font-size: 12px;
        font-family: 'Montserrat', sans-serif;
        outline: none; box-sizing: border-box;
        background: #fafafa; color: #374151;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    /* Input saat fokus */
    .form-input:focus {
        border-color: #980404; background: #fff;
        box-shadow: 0 0 0 3px rgba(152,4,4,0.07);
    }
    /* Input dengan kondisi error */
    .form-input.is-error { border-color: #dc2626; }

    /* Label field */
    .field-label {
        display: block; margin-bottom: 3px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600; font-size: 12px; color: #980404;
    }
    .field-label span { color: #dc2626; } /* Tanda bintang wajib isi */

    /* Teks pesan error per field */
    .field-error { margin: 2px 0 0; font-size: 11px; color: #dc2626; }

    /* Dropdown hasil pencarian debitur */
    #hasilCari {
        display: none; margin-top: 4px;
        border: 1.5px solid rgba(152,4,4,0.15); border-radius: 7px;
        overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        max-height: 220px; overflow-y: auto;
        position: absolute; width: 100%; z-index: 100; background: #fff;
    }
    /* Setiap item hasil pencarian */
    .hasil-item {
        padding: 8px 12px;
        cursor: pointer; background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-family: 'Montserrat', sans-serif; transition: background 0.15s;
    }
    .hasil-item:last-child { border-bottom: none; }
    .hasil-item:hover { background: rgba(152,4,4,0.04); }
    .hasil-item .nama { font-size: 12px; font-weight: 700; color: #374151; }
    .hasil-item .meta { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    /* Kotak referensi (readonly, diisi otomatis dari data terpilih) */
    .ref-box {
        width: 100%; padding: 6px 10px;
        border: 1.5px solid rgba(0,0,0,0.08); border-radius: 8px;
        font-size: 12px; font-family: 'Montserrat', sans-serif;
        background: #f3f4f6; color: #374151; font-weight: 600;
        box-sizing: border-box; min-height: 30px;
        display: flex; align-items: center;
    }
    /* Ref-box sebelum data dipilih */
    .ref-box.empty { color: #c0c7d0; font-weight: 400; font-style: italic; }

    /* Pemisah antar bagian form */
    .section-divider {
        font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700;
        color: #9ca3af; text-transform: uppercase; letter-spacing: 0.6px;
        margin: 5px 0 5px;
        padding-bottom: 6px;
        border-bottom: 1px solid rgba(0,0,0,0.07);
    }

    /* Peringatan tanggal perpanjangan tidak valid */
    #warnTanggal {
        display: none; margin-top: 4px; padding: 6px 10px;
        background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px;
        font-family: 'Montserrat', sans-serif; font-size: 11px; color: #c2410c;
    }
</style>

{{-- Kotak pesan error validasi server --}}
@if ($errors->any())
<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;">
    <p style="margin: 0 0 4px; font-weight: 700; color: #dc2626; font-family: 'Montserrat', sans-serif; font-size: 11px;">
        <i class="fas fa-exclamation-circle"></i> Periksa isian berikut:
    </p>
    <ul style="margin: 0; padding-left: 16px; color: #dc2626; font-size: 11px; line-height: 1.8;">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<div class="card" style="padding: 24px;">
    <form action="{{ route('laporan-perpanjangan.store') }}"
        method="POST"
        id="formPerpanjangan"
        onsubmit="return validasiSebelumSubmit()">
        @csrf

        {{-- Hidden input untuk menyimpan data yang dipilih dari hasil pencarian --}}
        <input type="hidden" name="laporan_realisasi_id"     id="hiddenRealisasiId">
        <input type="hidden" name="no_akad"                  id="hiddenNoAkad">
        <input type="hidden" name="no_loan"                  id="hiddenNoLoan">
        <input type="hidden" name="nama_debitur"             id="hiddenNamaDebitur">
        <input type="hidden" name="berat_ref"                id="hiddenBerat">
        <input type="hidden" name="biaya_sewa_tambahan"      id="hiddenBiaya">
        <input type="hidden" name="tanggal_jatuh_tempo_baru" id="hiddenJatuhTempoBaru">
        <input type="hidden" id="hiddenJatuhTempoLama">

        {{-- ── BAGIAN 1: PILIH DEBITUR ── --}}
        <div class="section-divider">Data Realisasi Asal</div>

        {{-- Input pencarian nama debitur atau no. akad --}}
        <div style="margin-bottom: 16px; position: relative;">
            <label class="field-label">Nama Debitur <span>*</span></label>
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 12px; pointer-events: none;"></i>
                <input type="text" id="inputCari"
                    placeholder="Ketik nama debitur atau no. akad..."
                    autocomplete="off"
                    class="form-input"
                    style="padding-left: 32px;"
                    oninput="filterDebitur()">
            </div>

            {{-- Dropdown hasil pencarian (diisi dari data $daftarRealisasi) --}}
            <div id="hasilCari">
                @foreach($daftarRealisasi as $r)
                <div class="hasil-item"
                    data-id="{{ $r->id }}"
                    data-no-akad="{{ $r->no_akad }}"
                    data-no-loan="{{ $r->no_loan }}"
                    data-nama="{{ $r->nama_debitur }}"
                    data-berat="{{ $r->berat }}"
                    data-kadar="{{ $r->kadar }}"
                    data-taksiran="{{ $r->taksiran }}"
                    data-pembiayaan="{{ $r->pembiayaan }}"
                    data-jatuh-tempo="{{ \Carbon\Carbon::parse($r->tanggal_jatuh_tempo)->format('d/m/Y') }}"
                    data-jatuh-tempo-iso="{{ \Carbon\Carbon::parse($r->tanggal_jatuh_tempo)->format('Y-m-d') }}"
                    data-search="{{ strtolower($r->nama_debitur . ' ' . $r->no_akad) }}"
                    onclick="pilihDebitur(this)">
                    <div class="nama">{{ $r->nama_debitur }}</div>
                    <div class="meta">
                        {{ $r->no_akad }} &nbsp;·&nbsp; {{ $r->no_loan }}
                        &nbsp;·&nbsp; JT: {{ \Carbon\Carbon::parse($r->tanggal_jatuh_tempo)->format('d/m/Y') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Referensi: No. Akad & No. Loan (diisi otomatis) --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label class="field-label">No. Akad</label>
                <div class="ref-box empty" id="refNoAkad">—</div>
            </div>
            <div>
                <label class="field-label">No. Loan</label>
                <div class="ref-box empty" id="refNoLoan">—</div>
            </div>
        </div>

        {{-- Referensi: Berat & Kadar emas (diisi otomatis) --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label class="field-label">Berat (gram)</label>
                <div class="ref-box empty" id="refBerat">—</div>
            </div>
            <div>
                <label class="field-label">Kadar</label>
                <div class="ref-box empty" id="refKadar">—</div>
            </div>
        </div>

        {{-- Referensi: Taksiran & Pembiayaan (diisi otomatis) --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label class="field-label">Taksiran (Rp)</label>
                <div class="ref-box empty" id="refTaksiran">—</div>
            </div>
            <div>
                <label class="field-label">Pembiayaan (Rp)</label>
                <div class="ref-box empty" id="refPembiayaan">—</div>
            </div>
        </div>

        {{-- Referensi: Jatuh tempo dari akad asal (diisi otomatis) --}}
        <div style="margin-bottom: 16px;">
            <label class="field-label">Jatuh Tempo Lama</label>
            <div class="ref-box empty" id="refJatuhTempo">—</div>
        </div>

        {{-- ── BAGIAN 2: DATA PERPANJANGAN ── --}}
        <div class="section-divider">Data Perpanjangan</div>

        {{-- Input tanggal perpanjangan & jumlah bulan --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label class="field-label">Tanggal Perpanjangan <span>*</span></label>
                {{-- Input tampilan (format dd/mm/yyyy), dikonversi ke ISO oleh JS --}}
                <input type="text" id="inputTglPerpanjangan"
                    placeholder="dd/mm/yyyy"
                    value="{{ old('tanggal_perpanjangan') ? \Carbon\Carbon::parse(old('tanggal_perpanjangan'))->format('d/m/Y') : now()->format('d/m/Y') }}"
                    class="form-input {{ $errors->has('tanggal_perpanjangan') ? 'is-error' : '' }}"
                    maxlength="10"
                    oninput="formatInputTanggal(this)">
                {{-- Hidden input menyimpan nilai ISO (yyyy-mm-dd) untuk dikirim ke server --}}
                <input type="hidden" name="tanggal_perpanjangan" id="hiddenTglPerpanjangan"
                    value="{{ old('tanggal_perpanjangan', date('Y-m-d')) }}">
                @error('tanggal_perpanjangan')
                    <p class="field-error">{{ $message }}</p>
                @enderror
                {{-- Peringatan jika tanggal lebih awal dari jatuh tempo lama --}}
                <div id="warnTanggal">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="warnTanggalText"></span>
                </div>
            </div>
            <div>
                <label class="field-label">Jumlah Bulan <span>*</span></label>
                <input type="number" name="jumlah_bulan" id="inputJumlahBulan"
                    value="{{ old('jumlah_bulan') }}"
                    placeholder="cth: 3" min="1"
                    class="form-input {{ $errors->has('jumlah_bulan') ? 'is-error' : '' }}">
                @error('jumlah_bulan')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Jatuh tempo baru (dihitung otomatis: tanggal perpanjangan + jumlah bulan) --}}
        <div style="margin-bottom: 16px;">
            <label class="field-label">Tanggal Jatuh Tempo Baru</label>
            <div class="ref-box empty" id="refJatuhTempoBaru">—</div>
        </div>

        {{-- Preview kalkulasi biaya sewa tambahan --}}
        <div style="
            display: flex; align-items: center; justify-content: space-between;
            background: #fafafa; border: 1px dashed rgba(152,4,4,0.2);
            border-radius: 7px; padding: 10px 14px; margin-bottom: 28px;
        ">
            <div style="display: flex; align-items: center; gap: 7px;">
                <i class="fas fa-calculator" style="color: #980404; font-size: 11px;"></i>
                <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px;">Tambahan Biaya Sewa</span>
            </div>
            <span style="font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #980404;" id="previewBiaya">Rp 0</span>
        </div>

        {{-- Tombol aksi: Simpan, Batal (reset), dan Kembali --}}
        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="submit" style="
                padding: 8px 20px; background: #980404; color: #fff;
                border: none; cursor: pointer; border-radius: 7px;
                font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 12px;
                display: inline-flex; align-items: center; gap: 6px;
                box-shadow: 0 3px 8px rgba(152,4,4,0.22); transition: background 0.2s;"
                onmouseover="this.style.background='#7a0303'"
                onmouseout="this.style.background='#980404'">
                <i class="fas fa-save"></i> Simpan
            </button>
            {{-- Tombol batal: reset semua isian form --}}
            <button type="button" onclick="konfirmasiBatal()" style="
                padding: 8px 16px; background: transparent; color: #dc2626;
                border: 1.5px solid rgba(220,38,38,0.25); border-radius: 7px;
                font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 12px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
                transition: background 0.2s, border-color 0.2s;"
                onmouseover="this.style.background='rgba(220,38,38,0.05)'; this.style.borderColor='rgba(220,38,38,0.5)'"
                onmouseout="this.style.background='transparent'; this.style.borderColor='rgba(220,38,38,0.25)'">
                <i class="fas fa-times" style="font-size: 11px;"></i> Batal
            </button>
            {{-- Tautan kembali ke halaman daftar laporan --}}
            <a href="{{ route('data-laporan.index') }}" style="
                padding: 8px 16px; background: transparent; color: #6b7280;
                border: 1.5px solid rgba(0,0,0,0.10); border-radius: 7px;
                font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 12px;
                text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
                transition: color 0.2s, border-color 0.2s;"
                onmouseover="this.style.color='#374151'; this.style.borderColor='rgba(0,0,0,0.25)'"
                onmouseout="this.style.color='#6b7280'; this.style.borderColor='rgba(0,0,0,0.10)'">
                <i class="fas fa-arrow-left" style="font-size: 11px;"></i> Kembali
            </a>
        </div>

    </form>
</div>

<script>
// Variabel global: berat emas referensi & ISO jatuh tempo lama
let beratRef      = 0;
let jatuhTempoIso = '';

// Konversi format ISO (yyyy-mm-dd) ke tampilan (dd/mm/yyyy)
function isoKeTampilan(iso) {
    if (!iso) return '—';
    const [y, m, d] = iso.split('-');
    return d + '/' + m + '/' + y;
}

// Konversi format tampilan (dd/mm/yyyy) ke ISO (yyyy-mm-dd)
function tampilanKeIso(tgl) {
    if (!tgl || tgl.length !== 10) return '';
    const [d, m, y] = tgl.split('/');
    return y + '-' + m + '-' + d;
}

// Format angka ke tampilan Rupiah
function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

// Format otomatis input tanggal ke dd/mm/yyyy saat mengetik
function formatInputTanggal(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 8);
    if (v.length >= 5)      v = v.substring(0,2) + '/' + v.substring(2,4) + '/' + v.substring(4);
    else if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
    input.value = v;
    // Sinkronkan nilai ISO ke hidden input
    document.getElementById('hiddenTglPerpanjangan').value = tampilanKeIso(v);
    onTanggalPerpanjanganChange();
}

// Tambahkan n bulan ke tanggal ISO, kembalikan tanggal ISO baru
function tambahBulan(isoDate, n) {
    const [y, m, d] = isoDate.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    dt.setMonth(dt.getMonth() + n);
    return dt.getFullYear()
        + '-' + String(dt.getMonth() + 1).padStart(2, '0')
        + '-' + String(dt.getDate()).padStart(2, '0');
}

// Isi ref-box dengan nilai dan hapus kelas 'empty'
function setRef(id, val) {
    const el = document.getElementById(id);
    el.textContent = val;
    el.classList.remove('empty');
}

// Filter dropdown hasil pencarian debitur berdasarkan input
function filterDebitur() {
    const q         = document.getElementById('inputCari').value.toLowerCase().trim();
    const container = document.getElementById('hasilCari');
    const items     = document.querySelectorAll('.hasil-item');
    if (!q) { container.style.display = 'none'; return; }
    container.style.display = 'block';
    let ada = false;
    items.forEach(item => {
        const match = item.dataset.search.includes(q);
        item.style.display = match ? 'block' : 'none';
        if (match) ada = true;
    });
    // Sembunyikan dropdown jika tidak ada hasil
    if (!ada) container.style.display = 'none';
}

// Isi semua ref-box dengan data debitur yang dipilih
function pilihDebitur(el) {
    beratRef      = parseFloat(el.dataset.berat) || 0;
    jatuhTempoIso = el.dataset.jatuhTempoIso;

    // Isi hidden input
    document.getElementById('hiddenRealisasiId').value    = el.dataset.id;
    document.getElementById('hiddenNoAkad').value         = el.dataset.noAkad;
    document.getElementById('hiddenNoLoan').value         = el.dataset.noLoan;
    document.getElementById('hiddenNamaDebitur').value    = el.dataset.nama;
    document.getElementById('hiddenBerat').value          = beratRef;
    document.getElementById('hiddenJatuhTempoLama').value = jatuhTempoIso;

    // Tampilkan data ke ref-box
    setRef('refNoAkad',     el.dataset.noAkad);
    setRef('refNoLoan',     el.dataset.noLoan);
    setRef('refBerat',      parseFloat(el.dataset.berat).toFixed(2) + ' gram');
    setRef('refKadar',      el.dataset.kadar);
    setRef('refTaksiran',   formatRupiah(el.dataset.taksiran));
    setRef('refPembiayaan', formatRupiah(el.dataset.pembiayaan));
    setRef('refJatuhTempo', el.dataset.jatuhTempo);

    // Tutup dropdown dan isi input pencarian dengan nama terpilih
    document.getElementById('inputCari').value         = el.dataset.nama;
    document.getElementById('hasilCari').style.display = 'none';

    // Hitung ulang biaya dengan data baru
    onTanggalPerpanjanganChange();
}

// Validasi tanggal perpanjangan: tidak boleh sebelum jatuh tempo lama
function onTanggalPerpanjanganChange() {
    const tglValue = document.getElementById('hiddenTglPerpanjangan').value;
    const warn     = document.getElementById('warnTanggal');
    const inputTgl = document.getElementById('inputTglPerpanjangan');

    if (jatuhTempoIso && tglValue && tglValue < jatuhTempoIso) {
        // Tampilkan peringatan
        document.getElementById('warnTanggalText').textContent =
            'Tanggal perpanjangan tidak boleh sebelum jatuh tempo lama (' + isoKeTampilan(jatuhTempoIso) + ').';
        warn.style.display = 'block';
        inputTgl.classList.add('is-error');
    } else {
        warn.style.display = 'none';
        inputTgl.classList.remove('is-error');
    }

    hitungBiaya();
}

// Hitung biaya sewa tambahan dan jatuh tempo baru
function hitungBiaya() {
    const bulan = parseInt(document.getElementById('inputJumlahBulan').value) || 0;
    const tgl   = document.getElementById('hiddenTglPerpanjangan').value;

    if (tgl && bulan > 0) {
        // Hitung dan tampilkan jatuh tempo baru
        const jatuhTempoBaru = tambahBulan(tgl, bulan);
        setRef('refJatuhTempoBaru', isoKeTampilan(jatuhTempoBaru));
        document.getElementById('hiddenJatuhTempoBaru').value = jatuhTempoBaru;
    } else {
        // Kosongkan jika data belum lengkap
        const el = document.getElementById('refJatuhTempoBaru');
        el.textContent = '—'; el.classList.add('empty');
        document.getElementById('hiddenJatuhTempoBaru').value = '';
    }

    // Hitung biaya: berat × tarif ujrah × jumlah bulan
    const tarifUjrah = {{ $tarifUjrah }};
    const biaya = beratRef * tarifUjrah * bulan;

    document.getElementById('previewBiaya').textContent = 'Rp ' + (biaya > 0 ? biaya.toLocaleString('id-ID') : '0');
    document.getElementById('hiddenBiaya').value        = biaya;
}

// Validasi form sebelum submit
function validasiSebelumSubmit() {
    const tglValue = document.getElementById('hiddenTglPerpanjangan').value;

    // Pastikan debitur sudah dipilih
    if (!document.getElementById('hiddenRealisasiId').value) {
        alert('Pilih debitur terlebih dahulu.');
        document.getElementById('inputCari').focus();
        return false;
    }
    // Pastikan tanggal perpanjangan tidak sebelum jatuh tempo lama
    if (jatuhTempoIso && tglValue && tglValue < jatuhTempoIso) {
        alert('Tanggal perpanjangan tidak boleh sebelum jatuh tempo lama (' + isoKeTampilan(jatuhTempoIso) + ').');
        document.getElementById('inputTglPerpanjangan').focus();
        return false;
    }
    return true;
}

// Reset semua ref-box dan variabel ke kondisi awal
function resetRefBoxes() {
    beratRef      = 0;
    jatuhTempoIso = '';

    // Kosongkan semua ref-box
    ['refNoAkad','refNoLoan','refBerat','refKadar','refTaksiran','refPembiayaan','refJatuhTempo','refJatuhTempoBaru'].forEach(id => {
        const el = document.getElementById(id);
        el.textContent = '—'; el.classList.add('empty');
    });

    // Reset nilai preview dan hidden input
    document.getElementById('previewBiaya').textContent        = 'Rp 0';
    document.getElementById('hiddenBiaya').value               = 0;
    document.getElementById('hiddenJatuhTempoLama').value      = '';
    document.getElementById('hiddenTglPerpanjangan').value     = '';
    document.getElementById('inputTglPerpanjangan').value      = '';
    document.getElementById('hasilCari').style.display         = 'none';
    document.getElementById('inputTglPerpanjangan').classList.remove('is-error');
    document.getElementById('warnTanggal').style.display       = 'none';
}

// Konfirmasi reset form saat klik tombol Batal
function konfirmasiBatal() {
    if (confirm('Reset semua isian form?')) {
        document.getElementById('formPerpanjangan').reset();
        resetRefBoxes();
    }
}

// Hitung ulang biaya setiap kali jumlah bulan berubah
document.getElementById('inputJumlahBulan').addEventListener('input', hitungBiaya);

// Tutup dropdown hasil cari saat klik di luar area pencarian
document.addEventListener('click', function(e) {
    if (!e.target.closest('#inputCari') && !e.target.closest('#hasilCari')) {
        document.getElementById('hasilCari').style.display = 'none';
    }
});

// Inisialisasi: sinkronkan hidden ISO dari nilai awal input tanggal
(function () {
    const inputTgl = document.getElementById('inputTglPerpanjangan');
    document.getElementById('hiddenTglPerpanjangan').value = tampilanKeIso(inputTgl.value);
    hitungBiaya();
})();
</script>

@endsection