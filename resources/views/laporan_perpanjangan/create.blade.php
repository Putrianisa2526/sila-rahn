@extends('layouts.app')

@section('title', 'Tambah Laporan Perpanjangan')
@section('page-title', 'Tambah Laporan Perpanjangan')

@section('content')

{{-- SUCCESS TOAST --}}
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
@keyframes toastIn  { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes toastOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-8px); } }
</style>
<script>
function closeToast() {
    const t = document.getElementById('successToast');
    if (t) { t.style.animation = 'toastOut 0.25s ease'; setTimeout(() => t.remove(), 250); }
}
setTimeout(closeToast, 4500);
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('formPerpanjangan').reset();
    resetRefBoxes();
});
</script>
@endif

<style>
    .form-input {
        width: 100%; padding: 6px 10px; /* was: 8px 11px */
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
        display: block; margin-bottom: 3px; /* was: 4px */
        font-family: 'Montserrat', sans-serif;
        font-weight: 600; font-size: 12px; color: #980404;
    }
    .field-label span { color: #dc2626; }
    .field-error { margin: 2px 0 0; font-size: 11px; color: #dc2626; }

    #hasilCari {
        display: none; margin-top: 4px;
        border: 1.5px solid rgba(152,4,4,0.15); border-radius: 7px;
        overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        max-height: 220px; overflow-y: auto;
        position: absolute; width: 100%; z-index: 100; background: #fff;
    }
    .hasil-item {
        padding: 8px 12px; /* was: 10px 14px */
        cursor: pointer; background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-family: 'Montserrat', sans-serif; transition: background 0.15s;
    }
    .hasil-item:last-child { border-bottom: none; }
    .hasil-item:hover { background: rgba(152,4,4,0.04); }
    .hasil-item .nama { font-size: 12px; font-weight: 700; color: #374151; }
    .hasil-item .meta { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    .ref-box {
        width: 100%; padding: 6px 10px; /* was: 9px 12px */
        border: 1.5px solid rgba(0,0,0,0.08); border-radius: 8px;
        font-size: 12px; font-family: 'Montserrat', sans-serif;
        background: #f3f4f6; color: #374151; font-weight: 600;
        box-sizing: border-box; min-height: 30px; /* was: 37px */
        display: flex; align-items: center;
    }
    .ref-box.empty { color: #c0c7d0; font-weight: 400; font-style: italic; }

    .section-divider {
        font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700;
        color: #9ca3af; text-transform: uppercase; letter-spacing: 0.6px;
        margin: 5px 0 5px;
        padding-bottom: 6px;
        border-bottom: 1px solid rgba(0,0,0,0.07);
    }

    #warnTanggal {
        display: none; margin-top: 4px; padding: 6px 10px; /* was: 7px 11px */
        background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px;
        font-family: 'Montserrat', sans-serif; font-size: 11px; color: #c2410c;
    }
</style>

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

        {{-- Nama Debitur (search) --}}
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

        {{-- No. Akad & No. Loan --}}
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

        {{-- Berat & Kadar --}}
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

        {{-- Taksiran & Pembiayaan --}}
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

        {{-- Jatuh Tempo Lama --}}
        <div style="margin-bottom: 16px;">
            <label class="field-label">Jatuh Tempo Lama</label>
            <div class="ref-box empty" id="refJatuhTempo">—</div>
        </div>

        {{-- ── BAGIAN 2: DATA PERPANJANGAN ── --}}
        <div class="section-divider">Data Perpanjangan</div>

        {{-- Tanggal Perpanjangan & Jumlah Bulan --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
                <label class="field-label">Tanggal Perpanjangan <span>*</span></label>
                <input type="text" id="inputTglPerpanjangan"
                    placeholder="dd/mm/yyyy"
                    value="{{ old('tanggal_perpanjangan') ? \Carbon\Carbon::parse(old('tanggal_perpanjangan'))->format('d/m/Y') : now()->format('d/m/Y') }}"
                    class="form-input {{ $errors->has('tanggal_perpanjangan') ? 'is-error' : '' }}"
                    maxlength="10"
                    oninput="formatInputTanggal(this)">
                <input type="hidden" name="tanggal_perpanjangan" id="hiddenTglPerpanjangan"
                    value="{{ old('tanggal_perpanjangan', date('Y-m-d')) }}">
                @error('tanggal_perpanjangan')
                    <p class="field-error">{{ $message }}</p>
                @enderror
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

        {{-- Tanggal Jatuh Tempo Baru (otomatis) --}}
        <div style="margin-bottom: 16px;">
            <label class="field-label">Tanggal Jatuh Tempo Baru</label>
            <div class="ref-box empty" id="refJatuhTempoBaru">—</div>
        </div>

        {{-- Preview Biaya Sewa --}}
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
let beratRef      = 0;
let jatuhTempoIso = '';

function isoKeTampilan(iso) {
    if (!iso) return '—';
    const [y, m, d] = iso.split('-');
    return d + '/' + m + '/' + y;
}

function tampilanKeIso(tgl) {
    if (!tgl || tgl.length !== 10) return '';
    const [d, m, y] = tgl.split('/');
    return y + '-' + m + '-' + d;
}

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function formatInputTanggal(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 8);
    if (v.length >= 5)      v = v.substring(0,2) + '/' + v.substring(2,4) + '/' + v.substring(4);
    else if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
    input.value = v;
    document.getElementById('hiddenTglPerpanjangan').value = tampilanKeIso(v);
    onTanggalPerpanjanganChange();
}

function tambahBulan(isoDate, n) {
    const [y, m, d] = isoDate.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    dt.setMonth(dt.getMonth() + n);
    return dt.getFullYear()
        + '-' + String(dt.getMonth() + 1).padStart(2, '0')
        + '-' + String(dt.getDate()).padStart(2, '0');
}

function setRef(id, val) {
    const el = document.getElementById(id);
    el.textContent = val;
    el.classList.remove('empty');
}

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
    if (!ada) container.style.display = 'none';
}

function pilihDebitur(el) {
    beratRef      = parseFloat(el.dataset.berat) || 0;
    jatuhTempoIso = el.dataset.jatuhTempoIso;

    document.getElementById('hiddenRealisasiId').value    = el.dataset.id;
    document.getElementById('hiddenNoAkad').value         = el.dataset.noAkad;
    document.getElementById('hiddenNoLoan').value         = el.dataset.noLoan;
    document.getElementById('hiddenNamaDebitur').value    = el.dataset.nama;
    document.getElementById('hiddenBerat').value          = beratRef;
    document.getElementById('hiddenJatuhTempoLama').value = jatuhTempoIso;

    setRef('refNoAkad',     el.dataset.noAkad);
    setRef('refNoLoan',     el.dataset.noLoan);
    setRef('refBerat',      parseFloat(el.dataset.berat).toFixed(2) + ' gram');
    setRef('refKadar',      el.dataset.kadar);
    setRef('refTaksiran',   formatRupiah(el.dataset.taksiran));
    setRef('refPembiayaan', formatRupiah(el.dataset.pembiayaan));
    setRef('refJatuhTempo', el.dataset.jatuhTempo);

    document.getElementById('inputCari').value         = el.dataset.nama;
    document.getElementById('hasilCari').style.display = 'none';

    onTanggalPerpanjanganChange();
}

function onTanggalPerpanjanganChange() {
    const tglValue = document.getElementById('hiddenTglPerpanjangan').value;
    const warn     = document.getElementById('warnTanggal');
    const inputTgl = document.getElementById('inputTglPerpanjangan');

    if (jatuhTempoIso && tglValue && tglValue < jatuhTempoIso) {
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

function hitungBiaya() {
    const bulan = parseInt(document.getElementById('inputJumlahBulan').value) || 0;
    const tgl   = document.getElementById('hiddenTglPerpanjangan').value;

    if (tgl && bulan > 0) {
        const jatuhTempoBaru = tambahBulan(tgl, bulan);
        setRef('refJatuhTempoBaru', isoKeTampilan(jatuhTempoBaru));
        document.getElementById('hiddenJatuhTempoBaru').value = jatuhTempoBaru;
    } else {
        const el = document.getElementById('refJatuhTempoBaru');
        el.textContent = '—'; el.classList.add('empty');
        document.getElementById('hiddenJatuhTempoBaru').value = '';
    }

    const tarifUjrah = {{ $tarifUjrah }};
    const biaya = beratRef * tarifUjrah * bulan;

    document.getElementById('previewBiaya').textContent = 'Rp ' + (biaya > 0 ? biaya.toLocaleString('id-ID') : '0');
    document.getElementById('hiddenBiaya').value        = biaya;
}

function validasiSebelumSubmit() {
    const tglValue = document.getElementById('hiddenTglPerpanjangan').value;
    if (!document.getElementById('hiddenRealisasiId').value) {
        alert('Pilih debitur terlebih dahulu.');
        document.getElementById('inputCari').focus();
        return false;
    }
    if (jatuhTempoIso && tglValue && tglValue < jatuhTempoIso) {
        alert('Tanggal perpanjangan tidak boleh sebelum jatuh tempo lama (' + isoKeTampilan(jatuhTempoIso) + ').');
        document.getElementById('inputTglPerpanjangan').focus();
        return false;
    }
    return true;
}

function resetRefBoxes() {
    beratRef      = 0;
    jatuhTempoIso = '';

    ['refNoAkad','refNoLoan','refBerat','refKadar','refTaksiran','refPembiayaan','refJatuhTempo','refJatuhTempoBaru'].forEach(id => {
        const el = document.getElementById(id);
        el.textContent = '—'; el.classList.add('empty');
    });

    document.getElementById('previewBiaya').textContent        = 'Rp 0';
    document.getElementById('hiddenBiaya').value               = 0;
    document.getElementById('hiddenJatuhTempoLama').value      = '';
    document.getElementById('hiddenTglPerpanjangan').value     = '';
    document.getElementById('inputTglPerpanjangan').value      = '';
    document.getElementById('hasilCari').style.display         = 'none';
    document.getElementById('inputTglPerpanjangan').classList.remove('is-error');
    document.getElementById('warnTanggal').style.display       = 'none';
}

function konfirmasiBatal() {
    if (confirm('Reset semua isian form?')) {
        document.getElementById('formPerpanjangan').reset();
        resetRefBoxes();
    }
}

document.getElementById('inputJumlahBulan').addEventListener('input', hitungBiaya);

document.addEventListener('click', function(e) {
    if (!e.target.closest('#inputCari') && !e.target.closest('#hasilCari')) {
        document.getElementById('hasilCari').style.display = 'none';
    }
});

(function () {
    const inputTgl = document.getElementById('inputTglPerpanjangan');
    document.getElementById('hiddenTglPerpanjangan').value = tampilanKeIso(inputTgl.value);
    hitungBiaya();
})();
</script>

@endsection