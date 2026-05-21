<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home')</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/img/brk.png') }}">
    <link rel="apple-touch-icon"      href="{{ asset('assets/img/brk.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #fafafa;
        }

        /* --- Layout --- */
        .layout { display: flex; height: 100vh; }

        /* --- Sidebar --- */
        .sidebar {
            width: 250px;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 200;
            background: linear-gradient(180deg, rgba(255,250,245,.8), rgba(254,248,242,.75));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(230,220,210,.3);
            box-shadow: 4px 0 20px rgba(0,0,0,.03), inset 0 0 40px rgba(250,235,215,.2);
            transition: transform .3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            padding: 10px 20px;
        }

        .sidebar-header img {
            height: 50px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.1));
        }

        /* --- Menu --- */
        .menu { list-style: none; padding: 0; margin: 0; }

        .menu li {
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }

        .menu li:last-child { border-bottom: none; }

        .menu a {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #980404;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 600;
            transition: all .25s ease;
        }

        .menu a i { width: 22px; text-align: center; font-size: 15px; flex-shrink: 0; }
        .menu a:hover { color: #5c0a0a; transform: translateX(3px); }
        .menu a.active { color: #f8a51a; }
        .menu a.active::before {
            content: "";
            position: absolute;
            left: -20px;
            height: 100%;
            width: 4px;
            background: #f8a51a;
            border-radius: 4px;
        }

        /* --- Main --- */
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

        /* --- Topbar --- */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            height: 60px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, rgba(255,250,245,.85), rgba(254,248,242,.8), rgba(255,252,248,.85));
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-bottom: 1px solid rgba(230,220,210,.3);
            box-shadow: 0 2px 15px rgba(0,0,0,.02), inset 0 1px 0 rgba(255,255,255,.6);
        }

        .topbar-left { display: flex; align-items: center; gap: 14px; }

        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            color: #980404;
            font-size: 20px;
            transition: background .2s;
        }
        .hamburger:hover { background: rgba(152,4,4,.08); }

        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: #980404;
        }

        .logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 13px;
            color: #980404;
            background: rgba(255,255,255,.5);
            backdrop-filter: blur(10px);
            transition: all .3s ease;
        }
        .logout:hover {
            color: #5c0a0a;
            background: rgba(255,255,255,.7);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(92,10,10,.12);
        }

        /* --- Overlay --- */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 199;
            background: rgba(0,0,0,.35);
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { display: block; }

        /* --- Content & Card --- */
        .content { flex: 1; padding: 35px; overflow-y: auto; }

        .card {
            padding: 30px;
            border-radius: 16px;
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
        }
        .card h3 { margin-top: 0; color: #980404; font-family: 'Montserrat', sans-serif; }

        /* --- User Info Card --- */
        .user-info-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 25px 30px;
            margin-bottom: 30px;
            border-radius: 16px;
            background: #980404;
            box-shadow: 0 8px 20px rgba(196,30,58,.15);
        }
        .user-avatar { font-size: 60px; color: rgba(255,255,255,.9); }
        .user-details h2 { margin: 0 0 8px; font-family: 'Montserrat', sans-serif; font-size: 24px; color: #fff; }
        .user-details p  { margin: 4px 0; font-size: 14px; color: rgba(255,255,255,.85); }
        .user-role, .last-login { display: flex; align-items: center; gap: 8px; }

        /* --- Stats --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 20px;
            border-radius: 14px;
            background: rgba(255,255,255,.9);
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
            transition: all .3s ease;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,.12); }
        .stat-icon {
            width: 60px; height: 60px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 24px; color: white;
        }
        .stat-content h3 { margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #7a5d4a; }
        .stat-number { margin: 0; font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 700; color: #980404; }
        .stat-label  { font-size: 12px; color: #9d8875; }

        /* --- Main Grid --- */
        .main-grid { display: grid; grid-template-columns: 1fr 400px; gap: 20px; }
        .left-column, .right-column { display: flex; flex-direction: column; gap: 20px; }

        /* --- Quick Actions --- */
        .quick-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .action-btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            padding: 16px 20px; border: none; border-radius: 10px;
            cursor: pointer; text-decoration: none;
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 13px; color: white;
            transition: all .3s ease;
        }
        .action-btn i { font-size: 16px; }
        .action-btn.primary   { background: #980404; box-shadow: 0 4px 12px rgba(152,4,4,.25); }
        .action-btn.secondary { background: #f8a51a; box-shadow: 0 4px 12px rgba(154,134,0,.25); }
        .action-btn.tertiary  { background: #2563eb; box-shadow: 0 4px 12px rgba(37,99,235,.25); }
        .action-btn.info      { background: #059669; box-shadow: 0 4px 12px rgba(5,150,105,.25); }
        .action-btn.primary:hover   { background: #5c0a0a; transform: translateY(-2px); }
        .action-btn.secondary:hover { background: #e08e00; transform: translateY(-2px); }
        .action-btn.tertiary:hover  { background: #1a3d99; transform: translateY(-2px); }
        .action-btn.info:hover      { background: #065f46; transform: translateY(-2px); }

        /* --- Activity --- */
        .activity-list { margin-top: 20px; }
        .activity-item { display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid rgba(0,0,0,.06); }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 16px; color: white;
        }
        .activity-icon.new      { background: #980404; }
        .activity-icon.extended { background: #e08e00; }
        .activity-content { flex: 1; }
        .activity-title { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #980404; }
        .activity-desc  { margin: 0 0 4px; font-size: 13px; color: #7a5d4a; }
        .activity-time  { font-size: 12px; color: #9d8875; }

        /* --- Calendar --- */
        .calendar-widget { margin-top: 20px; }
        .calendar-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .calendar-header h4 { margin: 0; font-family: 'Montserrat', sans-serif; color: #980404; }
        .calendar-nav {
            width: 32px; height: 32px; border: none; border-radius: 8px;
            cursor: pointer; background: rgba(152,4,4,.1); color: #980404; transition: all .3s ease;
        }
        .calendar-nav:hover { background: rgba(92,10,10,.18); }
        .calendar-days, .calendar-dates { display: grid; grid-template-columns: repeat(7,1fr); gap: 8px; text-align: center; }
        .calendar-days  { margin-bottom: 10px; }
        .calendar-days span { font-size: 12px; font-weight: 600; color: #7a5d4a; }
        .calendar-dates span { padding: 10px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: all .2s ease; }
        .calendar-dates span:hover:not(.empty) { background: rgba(92,10,10,.1); }
        .calendar-dates span.empty     { cursor: default; }
        .calendar-dates span.today     { background: #980404; color: white; font-weight: 700; }
        .calendar-dates span.has-event { background: rgba(154,134,0,.2); color: #e08e00; font-weight: 600; }

        /* --- Reminders --- */
        .reminders { margin-top: 25px; padding-top: 25px; border-top: 1px solid rgba(0,0,0,.1); }
        .reminders h4 { margin: 0 0 15px; font-family: 'Montserrat', sans-serif; font-size: 15px; color: #980404; }
        .reminder-item { display: flex; align-items: flex-start; gap: 12px; padding: 12px; border-radius: 10px; margin-bottom: 10px; }
        .reminder-item i { font-size: 18px; margin-top: 2px; }
        .reminder-item.urgent  { background: rgba(92,10,10,.08);   color: #5c0a0a; }
        .reminder-item.warning { background: rgba(184,149,106,.15); color: #7a5d4a; }
        .reminder-item.info    { background: rgba(166,124,82,.15);  color: #7a5d4a; }
        .reminder-title { margin: 0 0 4px; font-size: 13px; font-weight: 600; }
        .reminder-date  { font-size: 12px; opacity: .8; }

        /* --- Responsive --- */
        @media (max-width: 1200px) {
            .main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed; top: 0; left: 0;
                height: 100vh; transform: translateX(-100%);
            }
            .sidebar.open { transform: translateX(0); box-shadow: 6px 0 30px rgba(0,0,0,.15); }
            .hamburger { display: block; }
            .topbar { padding: 0 16px; }
            .content { padding: 16px; }
            .card { padding: 16px; }
            .user-info-card { flex-direction: column; text-align: center; padding: 20px 16px; }
            .user-role, .last-login { justify-content: center; }
            .user-details h2 { font-size: 18px; }
            .user-avatar { font-size: 44px; }
            .stats-grid { grid-template-columns: 1fr; gap: 12px; }
            .quick-actions { gap: 8px; }
            .action-btn { font-size: 12px; padding: 12px 8px; }
            .logout .logout-text { display: none; }
        }

        @media print {
            html, body { height: auto !important; overflow: visible !important; }
            .layout { display: block !important; height: auto !important; overflow: visible !important; }
            .sidebar, .topbar, .sidebar-overlay { display: none !important; }
            .main { display: block !important; height: auto !important; overflow: visible !important; }
            .content { display: block !important; overflow: visible !important; height: auto !important; max-height: none !important; padding: 0 !important; }
            .card { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }
        }

        @stack('styles')
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="layout">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('assets/img/brk.png') }}" alt="Logo BRK">
        </div>
        <ul class="menu">
            <li>
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li>
                <a href="{{ route('laporan-realisasi.create') }}" class="{{ request()->routeIs('laporan-realisasi.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i> Input Laporan
                </a>
            </li>
            <li>
                <a href="{{ route('laporan-perpanjangan.create') }}" class="{{ request()->routeIs('laporan-perpanjangan.create') ? 'active' : '' }}">
                    <i class="fas fa-redo"></i> Input Laporan Perpanjangan
                </a>
            </li>
            <li>
                <a href="{{ route('data-laporan.index') }}" class="{{ request()->routeIs('data-laporan.index') ? 'active' : '' }}">
                    <i class="fas fa-database"></i> Data Laporan
                </a>
            </li>
        </ul>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Buka menu">
                    <i class="fas fa-bars" id="hamburgerIcon"></i>
                </button>
                <div class="page-title">@yield('page-title', 'Home')</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="logout-text">Logout</span>
                </button>
            </form>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>

</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.contains('open') ? closeSidebar() : openSidebar();
    }
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('active');
        document.getElementById('hamburgerIcon').className = 'fas fa-times';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.getElementById('hamburgerIcon').className = 'fas fa-bars';
    }
    window.addEventListener('resize', () => { if (window.innerWidth > 768) closeSidebar(); });

    function updateClock() {
        const el = document.getElementById('liveClock');
        if (!el) return;
        const now = new Date();
        el.innerHTML = now.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) + ', ' + now.toLocaleTimeString('id-ID');
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

@stack('scripts')

</body>
</html>