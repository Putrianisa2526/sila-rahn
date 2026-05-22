<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Informasi Laporan Realisasi Rahn</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/img/brk.png') }}">

    {{-- Google Fonts: Inter & Montserrat --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome untuk ikon --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Reset dasar agar semua elemen konsisten */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* Background halaman dengan gambar penuh layar */
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: url('{{ asset("assets/img/bg_rahn.jpg") }}') center center / cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: flex-end; /* Kartu login di sisi kanan */
            padding: 40px 6vw;
        }

        /* Kartu utama login */
        .login-card {
            width: 100%;
            max-width: 360px;
            background: #fff;
            border-radius: 12px;
            padding: 36px 32px 28px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.13);
        }

        /* Area logo perusahaan */
        .login-logo {
            text-align: center;
            margin-bottom: 6px;
        }
        .login-logo img {
            height: 52px;
            object-fit: contain;
        }

        /* Judul sistem */
        .login-title {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: #980404;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 16px;
        }

        /* Teks panduan login */
        .login-subtitle {
            background: #fef2f2;
            border-left: 3px solid #980404;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 11px;
            color: #6b7280;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        /* Kotak pesan error validasi */
        .alert-error {
            background: #fef2f2;
            border-left: 3px solid #dc2626;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 16px;
        }
        .alert-error ul {
            margin: 0;
            padding-left: 14px;
            color: #dc2626;
            font-size: 11px;
            font-family: 'Montserrat', sans-serif;
            line-height: 1.8;
        }

        /* Grup form (setiap baris input) */
        .form-group { margin-bottom: 12px; }

        /* Gaya input teks umum */
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid rgba(0,0,0,0.10);
            border-radius: 8px;
            font-size: 12px;
            font-family: 'Inter', sans-serif;
            color: #374151;
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input::placeholder { color: #c0c7d0; }

        /* Input saat sedang aktif/fokus */
        .form-input:focus {
            border-color: #980404;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(152,4,4,0.07);
        }

        /* Input dengan kondisi error */
        .form-input.is-invalid { border-color: #dc2626; }

        /* Wrapper input password + tombol tampil/sembunyikan */
        .pw-wrapper { position: relative; }
        .pw-wrapper .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9d8875;
            font-size: 13px;
            padding: 0;
        }
        .pw-wrapper .toggle-pw:hover { color: #980404; }
        .pw-wrapper .form-input { padding-right: 36px; }

        /* Tombol submit login */
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #980404;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s, transform 0.15s;
            box-shadow: 0 4px 12px rgba(152,4,4,0.25);
        }
        .btn-login:hover  { background: #7a0303; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        /* Footer hak cipta */
        .login-footer {
            text-align: center;
            margin-top: 18px;
            font-size: 10px;
            color: #9d8875;
            font-family: 'Montserrat', sans-serif;
        }

        /* Responsif untuk layar kecil (mobile) */
        @media (max-width: 520px) {
            body { justify-content: center; padding: 24px 16px; }
            .login-card { padding: 28px 20px 22px; }
        }
    </style>
</head>
<body>

{{-- Kartu Login --}}
<div class="login-card">

    {{-- Logo BRK Syariah --}}
    <div class="login-logo">
        <img src="{{ asset('assets/img/logo_brksyariah.png') }}" alt="BRK Syariah">
    </div>

    {{-- Nama sistem --}}
    <p class="login-title">Sistem Informasi Laporan Realisasi Rahn</p>

    {{-- Petunjuk login --}}
    <div class="login-subtitle">
        Silahkan login menggunakan akun Sistem Informasi Laporan Realisasi Rahn.
    </div>

    {{-- Tampilkan pesan error jika ada --}}
    @if($errors->any())
    <div class="alert-error">
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form login --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Input username --}}
        <div class="form-group">
            <input type="text" name="username"
                value="{{ old('username') }}"
                placeholder="Username"
                class="form-input {{ $errors->has('username') ? 'is-invalid' : '' }}"
                autofocus autocomplete="username">
        </div>

        {{-- Input password dengan tombol show/hide --}}
        <div class="form-group">
            <div class="pw-wrapper">
                <input type="password" name="password" id="inputPassword"
                    placeholder="Password"
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    autocomplete="current-password">
                <button type="button" class="toggle-pw" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        {{-- Tombol login --}}
        <button type="submit" class="btn-login">Login</button>
    </form>

    {{-- Footer hak cipta --}}
    <p class="login-footer">&copy; {{ date('Y') }} PT. Bank Riau Kepri Syariah (Perseroda)</p>

</div>

<script>
    // Fungsi untuk menampilkan atau menyembunyikan password
    function togglePassword() {
        const input = document.getElementById('inputPassword');
        const icon  = document.getElementById('toggleIcon');

        if (input.type === 'password') {
            // Tampilkan password
            input.type     = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            // Sembunyikan password
            input.type     = 'password';
            icon.className = 'fas fa-eye';
        }
    }
</script>

</body>
</html>