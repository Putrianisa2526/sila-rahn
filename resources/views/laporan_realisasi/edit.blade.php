@extends('layouts.app')

@section('title', 'Edit Laporan Realisasi')
@section('page-title', 'Edit Laporan Realisasi')

@section('content')
<style>
    .form-group { margin-bottom: 16px; }
    .form-label {
        display: block; font-family: 'Montserrat', sans-serif;
        font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;
    }
    .form-control {
        width: 100%; padding: 9px 12px; border: 1.5px solid rgba(0,0,0,0.12);
        border-radius: 8px; font-size: 12px; font-family: 'Montserrat', sans-serif;
        color: #374151; background: #fafafa; outline: none;
        transition: border-color 0.2s; box-sizing: border-box;
    }
    .form-control:focus { border-color: #980404; background: #fff; }
    .form-control.is-invalid { border-color: #dc2626; }
    .invalid-feedback {
        font-size: 11px; color: #dc2626; font-family: 'Montserrat', sans-serif;
        margin-top: 4px; display: block;
    }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }
    @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
    .section-title {
        font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700;
        color: #980404; text-transform: uppercase; letter-spacing: 0.5px;
        margin: 20px 0 12px; padding-bottom: 6px;
        border-bottom: 1.5px solid rgba(152,4,4,0.15);
    }
</style>

@if($errors->any())
<div style="background:#fef2f2;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
    <p style="font-family:'Montserrat',sans-serif;font-size:12px;color:#dc2626;margin:0 0 6px;font-weight:600;">
        <i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:
    </p>
    <ul style="margin:0;padding-left:18px;">
        @foreach($errors->all() as $e)
            <li style="font-size:12px;color:#dc2626;font-family:'Montserrat',sans-serif;">{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card" style="padding: 24px;">
    <form method="POST" action="{{ route('laporan-realisasi.update', $realisasi->id) }}">
        @csrf @method('PUT')

        <p class="section-title">Informasi Akad</p>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">No. Akad <span style="color:#dc2626">*</span></label>
                <input type="text" name="no_akad" class="form-control {{ $errors->has('no_akad') ? 'is-invalid' : '' }}"
                    value="{{ old('no_akad', $realisasi->no_akad) }}" required>
                @error('no_akad')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">No. Loan</label>
                <input type="text" name="no_loan" class="form-control {{ $errors->has('no_loan') ? 'is-invalid' : '' }}"
                    value="{{ old('no_loan', $realisasi->no_loan) }}">
                @error('no_loan')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Nama Debitur <span style="color:#dc2626">*</span></label>
                <input type="text" name="nama_debitur" class="form-control {{ $errors->has('nama_debitur') ? 'is-invalid' : '' }}"
                    value="{{ old('nama_debitur', $realisasi->nama_debitur) }}" required>
                @error('nama_debitur')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>

        <p class="section-title">Detail Emas</p>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Berat (gram)</label>
                <input type="number" step="0.01" name="berat" class="form-control {{ $errors->has('berat') ? 'is-invalid' : '' }}"
                    value="{{ old('berat', $realisasi->berat) }}">
                @error('berat')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Kadar (Karat)</label>
                <input type="number" name="kadar" class="form-control {{ $errors->has('kadar') ? 'is-invalid' : '' }}"
                    value="{{ old('kadar', $realisasi->kadar) }}">
                @error('kadar')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Taksiran (Rp)</label>
                <input type="number" name="taksiran" class="form-control {{ $errors->has('taksiran') ? 'is-invalid' : '' }}"
                    value="{{ old('taksiran', $realisasi->taksiran) }}">
                @error('taksiran')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Pembiayaan (Rp)</label>
                <input type="number" name="pembiayaan" class="form-control {{ $errors->has('pembiayaan') ? 'is-invalid' : '' }}"
                    value="{{ old('pembiayaan', $realisasi->pembiayaan) }}">
                @error('pembiayaan')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Pendapatan Sewa (Rp)</label>
                <input type="number" name="pendapatan_sewa" class="form-control {{ $errors->has('pendapatan_sewa') ? 'is-invalid' : '' }}"
                    value="{{ old('pendapatan_sewa', $realisasi->pendapatan_sewa) }}">
                @error('pendapatan_sewa')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>

        <p class="section-title">Tanggal</p>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Tanggal Realisasi <span style="color:#dc2626">*</span></label>
                <input type="date" name="tanggal_realisasi" class="form-control {{ $errors->has('tanggal_realisasi') ? 'is-invalid' : '' }}"
                    value="{{ old('tanggal_realisasi', optional($realisasi->tanggal_realisasi)->format('Y-m-d')) }}" required>
                @error('tanggal_realisasi')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Jatuh Tempo</label>
                <input type="date" name="jatuh_tempo" class="form-control {{ $errors->has('jatuh_tempo') ? 'is-invalid' : '' }}"
                    value="{{ old('jatuh_tempo', optional($realisasi->jatuh_tempo)->format('Y-m-d')) }}">
                @error('jatuh_tempo')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;padding-top:16px;border-top:1px solid rgba(0,0,0,0.07);">
            <a href="{{ route('data-laporan.index') }}"
                style="padding:8px 18px;background:#f3f4f6;color:#6b7280;border-radius:8px;
                       font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;
                       text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit"
                style="padding:8px 20px;background:#980404;color:white;border:none;border-radius:8px;
                       font-size:12px;font-family:'Montserrat',sans-serif;font-weight:600;cursor:pointer;
                       display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection