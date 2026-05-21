@extends('layouts.app')

@section('title', 'Home')
@section('page-title', 'Home')

@section('content')

<style>
    .activity-sub {
        margin: 0 0 2px;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        font-family: 'Montserrat', sans-serif;
    }
    .calendar-dates span.end-month {
        background: rgba(154,134,0,.15);
        color: #e08e00;
        font-weight: 700;
    }
    .calendar-dates span.today.end-month {
        background: #980404;
        color: #fff;
    }

    @media (max-width: 900px) {
        .main-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .user-info-card { flex-direction: column; align-items: flex-start; gap: 10px; padding: 14px; }
        .user-avatar i  { font-size: 40px; }
        .user-details h2 { font-size: 16px; }
        .stats-grid { grid-template-columns: 1fr; }
        #chartRow { grid-template-columns: 1fr !important; }
        .main-grid { grid-template-columns: 1fr; }
        .left-column, .right-column { width: 100%; }
        .quick-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            align-items: stretch !important;
        }
        .action-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            width: 100% !important;
            font-size: 12px !important;
            padding: 12px 8px !important;
            white-space: normal !important;
        }
        .tarif-form { flex-wrap: wrap !important; gap: 6px !important; }
        .tarif-form input[type="number"] { width: 110px !important; }
        .card { padding: 12px !important; }
        .stat-card { flex-direction: row; align-items: center; gap: 12px; }
        .stat-number { font-size: 22px; }
        .calendar-widget { font-size: 12px; }
        .reminder-item { flex-direction: row; gap: 10px; }
    }
</style>

{{-- USER INFO --}}
<div class="user-info-card">
    <div class="user-avatar">
        <i class="fas fa-user-circle"></i>
    </div>
    <div class="user-details">
        <h2>Selamat Datang, {{ session('admin_name') ?? auth()->user()->name ?? 'Admin' }}</h2>
        <p class="user-role"><i class="fas fa-briefcase"></i> {{ session('admin_role') ?? auth()->user()->role ?? 'Administrator' }}</p>
        <p class="last-login"><i class="fas fa-clock"></i> <span id="liveClock"></span> WIB</p>
    </div>
</div>

{{-- STATISTICS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#980404;"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-content">
            <h3>Total Laporan Realisasi Rahn</h3>
            <p class="stat-number">{{ $totalRealisasi }}</p>
            <span class="stat-label">{{ $labelBulan }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f8a51a;"><i class="fas fa-redo"></i></div>
        <div class="stat-content">
            <h3>Jumlah Laporan Perpanjangan</h3>
            <p class="stat-number">{{ $totalPerpanjangan }}</p>
            <span class="stat-label">{{ $labelBulan }}</span>
        </div>
    </div>
</div>

{{-- CHARTS --}}
<div id="chartRow" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px;">
    <div class="card" style="padding:14px;">
        <h3 style="margin:0 0 2px;font-size:12px;"><i class="fas fa-chart-line"></i> Jumlah Laporan Per Bulan</h3>
        <p style="margin:0 0 10px;font-size:10px;color:#9d8875;">6 bulan terakhir</p>
        <canvas id="chartJumlah" height="160"></canvas>
    </div>
    <div class="card" style="padding:14px;">
        <h3 style="margin:0 0 2px;font-size:12px;"><i class="fas fa-chart-line"></i> Realisasi vs Perpanjangan</h3>
        <p style="margin:0 0 10px;font-size:10px;color:#9d8875;">6 bulan terakhir</p>
        <canvas id="chartPerbandingan" height="160"></canvas>
    </div>
    <div class="card" style="padding:14px;">
        <h3 style="margin:0 0 2px;font-size:12px;"><i class="fas fa-chart-bar"></i> Pendapatan/Biaya Sewa</h3>
        <p style="margin:0 0 10px;font-size:10px;color:#9d8875;">6 bulan terakhir</p>
        <canvas id="chartPendapatan" height="160"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels       = @json($chartLabels);
const realisasi    = @json($chartRealisasi);
const perpanjangan = @json($chartPerpanjangan);
const pendapatan   = @json($chartPendapatan);

const namaBulanMap = {
    'Januari':1,'Februari':2,'Maret':3,'April':4,'Mei':5,'Juni':6,
    'Juli':7,'Agustus':8,'September':9,'Oktober':10,'November':11,'Desember':12
};
const shortLabels = labels.map(l => {
    const [bln, thn] = l.split(' ');
    return namaBulanMap[bln] + '/' + thn.slice(2);
});

Chart.defaults.font = { family: 'Montserrat, sans-serif', size: 9 };

const scaleOpt = (isCurrency = false) => ({
    x: { grid: { display: false }, ticks: { font: { size: 9 } } },
    y: {
        beginAtZero: true,
        grid: { color: 'rgba(0,0,0,0.05)' },
        ticks: {
            font: { size: 9 },
            callback: isCurrency
                ? val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                : val => Number.isInteger(val) ? val : ''
        }
    }
});

new Chart(document.getElementById('chartJumlah'), {
    type: 'line',
    data: {
        labels: shortLabels,
        datasets: [{
            label: 'Total Laporan',
            data: realisasi.map((v, i) => v + perpanjangan[i]),
            borderColor: '#980404', backgroundColor: 'rgba(152,4,4,0.08)',
            borderWidth: 2, pointBackgroundColor: '#980404', pointRadius: 4,
            tension: 0.4, fill: true,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: scaleOpt() }
});

new Chart(document.getElementById('chartPerbandingan'), {
    type: 'line',
    data: {
        labels: shortLabels,
        datasets: [
            {
                label: 'Realisasi',
                data: realisasi,
                borderColor: '#980404', backgroundColor: 'rgba(152,4,4,0.06)',
                borderWidth: 2, pointBackgroundColor: '#980404', pointRadius: 4,
                tension: 0.4, fill: true,
            },
            {
                label: 'Perpanjangan',
                data: perpanjangan,
                borderColor: '#f8a51a', backgroundColor: 'rgba(248,165,26,0.06)',
                borderWidth: 2, pointBackgroundColor: '#f8a51a', pointRadius: 4,
                tension: 0.4, fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true, position: 'bottom', labels: { font: { size: 9 }, boxWidth: 10, padding: 8 } } },
        scales: scaleOpt()
    }
});

new Chart(document.getElementById('chartPendapatan'), {
    type: 'bar',
    data: {
        labels: shortLabels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: pendapatan,
            backgroundColor: 'rgba(30,64,175,0.75)',
            borderRadius: 5, borderSkipped: false,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: scaleOpt(true) }
});

// Responsive chart grid
(function() {
    var chartRow = document.getElementById('chartRow');
    function adjust() {
        var w = window.innerWidth;
        var cards = chartRow.children;
        if (w <= 600) {
            chartRow.style.gridTemplateColumns = '1fr';
            for (var i = 0; i < cards.length; i++) cards[i].style.gridColumn = '';
        } else if (w <= 900) {
            chartRow.style.gridTemplateColumns = '1fr 1fr';
            if (cards.length >= 3) cards[2].style.gridColumn = 'span 2';
        } else {
            chartRow.style.gridTemplateColumns = '1fr 1fr 1fr';
            for (var i = 0; i < cards.length; i++) cards[i].style.gridColumn = '';
        }
    }
    adjust();
    window.addEventListener('resize', adjust);
})();
</script>

{{-- MAIN GRID --}}
<div class="main-grid">

    {{-- LEFT --}}
    <div class="left-column">

        <div class="card">
            <div class="quick-actions">
                <a href="{{ route('laporan-realisasi.create') }}" class="action-btn primary">
                    <i class="fas fa-plus-circle"></i> Input Laporan Baru
                </a>
                <a href="{{ route('laporan-perpanjangan.create') }}" class="action-btn secondary">
                    <i class="fas fa-redo"></i> Input Perpanjangan
                </a>
                <a href="{{ route('data-laporan.index') }}" class="action-btn tertiary">
                    <i class="fas fa-list"></i> Lihat Data Laporan
                </a>
                <a href="{{ route('data-laporan.index', ['bulan' => $bulanIni, 'tahun' => $tahunIni, 'print' => '1']) }}"
                   target="_blank" class="action-btn info">
                    <i class="fas fa-print"></i> Cetak Laporan
                </a>
            </div>

            {{-- Tarif Ujrah --}}
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid #980404;">
                <p style="margin:0 0 8px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">
                    Tarif Ujrah
                </p>

                @if(session('success_tarif'))
                <div style="padding:7px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;margin-bottom:10px;font-size:11px;color:#15803d;font-family:'Montserrat',sans-serif;">
                    <i class="fas fa-check-circle"></i> {{ session('success_tarif') }}
                </div>
                @endif

                <form method="POST" action="{{ route('pengaturan.update') }}"
                      class="tarif-form" style="display:flex;align-items:center;gap:8px;">
                    @csrf
                    <span style="font-family:'Montserrat',sans-serif;font-size:12px;color:#374151;">Rp</span>
                    <input type="number" name="tarif_ujrah" value="{{ $tarifUjrah }}"
                        style="width:120px;padding:6px 10px;border:1.5px solid rgba(0,0,0,0.10);border-radius:7px;font-size:12px;font-family:'Montserrat',sans-serif;outline:none;background:#fafafa;"
                        onfocus="this.style.borderColor='#980404'"
                        onblur="this.style.borderColor='rgba(0,0,0,0.10)'">
                    <span style="font-family:'Montserrat',sans-serif;font-size:12px;color:#9d8875;">/ gram / bulan</span>
                    <button type="submit"
                        style="padding:6px 14px;background:#980404;color:#fff;border:none;border-radius:7px;font-size:12px;font-family:'Montserrat',sans-serif;font-weight:600;cursor:pointer;">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="card">
            <h3><i class="fas fa-history"></i> Laporan Terakhir Diinput</h3>
            <div class="activity-list">
                @forelse($activities as $activity)
                <div class="activity-item">
                    <div class="activity-icon {{ $activity['type'] === 'realisasi' ? 'new' : 'extended' }}">
                        <i class="fas {{ $activity['type'] === 'realisasi' ? 'fa-file-alt' : 'fa-redo' }}"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-title">{{ $activity['no_akad'] ?? '—' }}</p>
                        <p class="activity-sub">{{ $activity['nama_debitur'] ?? '—' }}</p>
                        <p class="activity-desc">{{ $activity['type'] === 'realisasi' ? 'Laporan Realisasi' : 'Laporan Perpanjangan' }}</p>
                        <span class="activity-time">{{ $activity['tanggal']->format('d M Y, H:i') }} WIB</span>
                    </div>
                </div>
                @empty
                <div class="activity-item">
                    <div class="activity-content">
                        <p class="activity-desc" style="text-align:center;color:#9d8875;padding:20px 0;">Belum ada aktivitas</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="right-column">
        <div class="card">
            <h3><i class="fas fa-calendar-alt"></i> Kalender & Reminder</h3>

            <div class="calendar-widget">
                <div class="calendar-header">
                    <button class="calendar-nav" onclick="calNav(-1)"><i class="fas fa-chevron-left"></i></button>
                    <h4 id="calTitle"></h4>
                    <button class="calendar-nav" onclick="calNav(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="calendar-days">
                    <span>Min</span><span>Sen</span><span>Sel</span>
                    <span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                </div>
                <div class="calendar-dates" id="calDates"></div>
            </div>

            <div class="reminders">
                <h4><i class="fas fa-bell"></i> Reminder</h4>
                <div class="reminder-item info">
                    <i class="fas fa-calendar-day"></i>
                    <div>
                        <p class="reminder-title">Hari Ini</p>
                        <span class="reminder-date" id="reminderHariIni"></span>
                    </div>
                </div>
                <div class="reminder-item urgent">
                    <i class="fas fa-print"></i>
                    <div>
                        <p class="reminder-title">Cetak Laporan Bulanan</p>
                        <span class="reminder-date" id="reminderAkhirBulan"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
const BULAN_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const today    = new Date();
let   calYear  = today.getFullYear();
let   calMonth = today.getMonth();

function renderCalendar() {
    const firstDay    = new Date(calYear, calMonth, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

    document.getElementById('calTitle').textContent = BULAN_ID[calMonth] + ' ' + calYear;

    const container = document.getElementById('calDates');
    container.innerHTML = '';

    for (let i = 0; i < firstDay; i++) {
        const s = document.createElement('span');
        s.classList.add('empty');
        container.appendChild(s);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const s = document.createElement('span');
        s.textContent = d;
        if (d === today.getDate() && calMonth === today.getMonth() && calYear === today.getFullYear()) s.classList.add('today');
        if (d === daysInMonth) s.classList.add('end-month');
        container.appendChild(s);
    }

    document.getElementById('reminderHariIni').textContent =
        today.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    document.getElementById('reminderAkhirBulan').textContent =
        'Deadline: ' + daysInMonth + ' ' + BULAN_ID[calMonth] + ' ' + calYear;
}

function calNav(dir) {
    calMonth += dir;
    if (calMonth > 11) { calMonth = 0; calYear++; }
    if (calMonth < 0)  { calMonth = 11; calYear--; }
    renderCalendar();
}

renderCalendar();
</script>

@endsection