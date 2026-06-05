<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan — QuickDine</title>
    <meta name="description" content="Buat akun QuickDine untuk menyimpan riwayat pesanan dan mempermudah transaksi Anda.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #B27C44;
            --primary-dark: #8B5A2B;
            --primary-light: #D4A76A;
            --secondary: #352214;
            --bg-warm: #FDFBF7;
            --bg-cream: #F7F0E6;
            --text-muted: #8C837C;
            --text-light: #A69C94;
            --border: #EAE3D9;
            --border-light: #F3EFE9;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-warm);
            color: var(--secondary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .auth-wrapper { display: flex; min-height: 100vh; }

        /* ── Desktop Side Panel ── */
        .side-panel {
            display: none;
            width: 50%;
            background: var(--secondary);
            position: relative;
            overflow: hidden;
        }

        .side-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&q=80') center/cover;
            opacity: 0.15;
        }

        .side-panel-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            height: 100%;
        }

        .side-logo {
            width: 56px; height: 56px;
            background: var(--primary);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 1.75rem; color: #fff;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 30px rgba(178,124,68,0.3);
        }

        .side-accent { width: 48px; height: 4px; background: var(--primary); border-radius: 4px; margin-bottom: 1.5rem; }
        .side-title { font-size: 2.75rem; font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 1rem; letter-spacing: -0.02em; }
        .side-subtitle { font-size: 1rem; color: var(--text-light); line-height: 1.6; max-width: 380px; font-weight: 500; }

        /* ── Main Panel ── */
        .main-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--bg-warm);
        }

        .brand-bar {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--secondary);
        }

        .brand-logo {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.1rem; color: #fff;
            flex-shrink: 0;
        }

        .brand-name { font-weight: 800; font-size: 1rem; color: #fff; letter-spacing: -0.01em; }

        .form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1.5rem 2rem;
        }

        .form-card {
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header { margin-bottom: 1.5rem; }
        .form-greeting { font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
        .form-title { font-size: 1.625rem; font-weight: 900; color: var(--secondary); letter-spacing: -0.02em; line-height: 1.2; margin-bottom: 0.5rem; }
        .form-desc { font-size: 0.875rem; color: var(--text-muted); font-weight: 500; line-height: 1.5; }

        /* ── Step Indicator ── */
        .step-indicator {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .step-bar {
            flex: 1;
            height: 4px;
            border-radius: 4px;
            background: var(--border);
            transition: background 0.3s;
        }

        .step-bar.active {
            background: var(--primary);
        }

        /* ── Alerts ── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .alert ul {
            list-style: disc;
            padding-left: 1rem;
            margin: 0;
        }

        .alert ul li + li { margin-top: 0.25rem; }
        .alert i { margin-top: 2px; flex-shrink: 0; }

        /* ── Form Elements ── */
        .field-group { margin-bottom: 1.125rem; }
        .field-label { display: block; font-size: 0.8125rem; font-weight: 700; color: var(--secondary); margin-bottom: 0.5rem; }
        .field-error { font-size: 0.6875rem; font-weight: 700; color: #dc2626; margin-top: 0.375rem; display: none; }
        .field-error.show { display: block; }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--text-light); font-size: 0.875rem; pointer-events: none; transition: color 0.2s;
        }

        .input-field {
            width: 100%;
            padding: 0.875rem 0.875rem 0.875rem 2.75rem;
            font-size: 0.875rem; font-weight: 500; color: var(--secondary);
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .input-field::placeholder { color: var(--text-light); font-weight: 400; }
        .input-field:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(178,124,68,0.1); }
        .input-wrapper:focus-within .input-icon { color: var(--primary); }
        .input-field-password { padding-right: 3rem; }

        .toggle-password {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--text-light);
            cursor: pointer; font-size: 0.875rem; padding: 4px; transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--primary); }

        /* ── Buttons ── */
        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; padding: 0.9375rem; font-size: 0.875rem; font-weight: 700;
            color: #fff; background: var(--secondary); border: none; border-radius: 12px;
            cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.01em; margin-top: 0.5rem;
        }
        .btn-primary:hover { background: #20150F; }
        .btn-primary:active { transform: scale(0.985); }

        .btn-secondary {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; padding: 0.9375rem; font-size: 0.875rem; font-weight: 700;
            color: #fff; background: var(--primary); border: none; border-radius: 12px;
            cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s;
            margin-top: 0.5rem;
        }
        .btn-secondary:hover { background: var(--primary-dark); }
        .btn-secondary:active { transform: scale(0.985); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 0.5rem;
            font-size: 0.8125rem; font-weight: 700; color: var(--text-muted);
            background: none; border: none; cursor: pointer; font-family: inherit;
            padding: 0; margin-bottom: 1rem; transition: color 0.2s;
        }
        .btn-back:hover { color: var(--secondary); }

        /* ── Divider ── */
        .divider { display: flex; align-items: center; gap: 0.75rem; margin: 1.5rem 0; }
        .divider-line { flex: 1; height: 1px; background: var(--border); }
        .divider-text { font-size: 0.6875rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

        /* ── Footer ── */
        .auth-footer { text-align: center; font-size: 0.8125rem; color: var(--text-muted); font-weight: 500; }
        .auth-footer a { color: var(--primary); font-weight: 700; text-decoration: none; transition: color 0.2s; }
        .auth-footer a:hover { color: var(--primary-dark); }
        .copyright { text-align: center; padding: 1.5rem; font-size: 0.625rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.15em; }

        .hidden { display: none !important; }

        @media (min-width: 1024px) {
            .side-panel { display: flex; }
            .brand-bar { display: none; }
            .form-container { padding: 3rem; }
            .form-title { font-size: 1.875rem; }
            .copyright { display: none; }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <!-- Desktop Side Panel -->
        <div class="side-panel">
            <div class="side-panel-content">
                <div class="side-logo">Q</div>
                <div class="side-accent"></div>
                <h1 class="side-title">Bergabung<br>Bersama Kami.</h1>
                <p class="side-subtitle">Satu akun untuk menyimpan semua riwayat pesanan lezat Anda dan mempermudah transaksi berikutnya.</p>
            </div>
        </div>

        <!-- Main Panel -->
        <div class="main-panel">
            <div class="brand-bar">
                <div class="brand-logo">Q</div>
                <span class="brand-name">QuickDine</span>
            </div>

            <div class="form-container">
                <div class="form-card">
                    <div class="form-header">
                        <p class="form-greeting">Buat Akun</p>
                        <h1 class="form-title">Daftar sebagai Pelanggan</h1>
                        <p class="form-desc">Hanya butuh beberapa langkah untuk memulai.</p>
                    </div>

                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step-bar active" id="indicator-1"></div>
                        <div class="step-bar" id="indicator-2"></div>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul>
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST" id="register-form">
                        @csrf

                        <!-- Step 1: Identity -->
                        <div id="step-1">
                            <div class="field-group">
                                <label for="name" class="field-label">Nama Lengkap</label>
                                <div class="input-wrapper">
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                        placeholder="Masukkan nama lengkap" class="input-field" style="padding-left: 2.75rem;">
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                                <p id="error-name" class="field-error">Nama harus diisi.</p>
                            </div>

                            <div class="field-group">
                                <label for="email" class="field-label">Email</label>
                                <div class="input-wrapper">
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="nama@email.com" class="input-field" style="padding-left: 2.75rem;">
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                                <p id="error-email" class="field-error">Email valid harus diisi.</p>
                            </div>

                            <button type="button" onclick="nextStep()" class="btn-secondary">
                                Lanjut <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>

                        <!-- Step 2: Security -->
                        <div id="step-2" class="hidden">
                            <button type="button" onclick="prevStep()" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>

                            <div class="field-group">
                                <label for="password" class="field-label">Kata Sandi</label>
                                <div class="input-wrapper">
                                    <input type="password" id="password" name="password"
                                        placeholder="Minimal 8 karakter" class="input-field input-field-password" style="padding-left: 2.75rem;">
                                    <i class="fas fa-lock input-icon"></i>
                                    <button type="button" onclick="togglePassword('password', 'eye-1')" class="toggle-password">
                                        <i id="eye-1" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="field-group">
                                <label for="password_confirmation" class="field-label">Konfirmasi Sandi</label>
                                <div class="input-wrapper">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        placeholder="Ulangi kata sandi" class="input-field input-field-password" style="padding-left: 2.75rem;">
                                    <i class="fas fa-check-circle input-icon"></i>
                                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-2')" class="toggle-password">
                                        <i id="eye-2" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="fas fa-user-plus" style="font-size: 0.75rem;"></i> Selesaikan Pendaftaran
                            </button>
                        </div>
                    </form>

                    <div class="divider">
                        <div class="divider-line"></div>
                        <span class="divider-text">atau</span>
                        <div class="divider-line"></div>
                    </div>

                    <p class="auth-footer">
                        Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>
                    </p>
                </div>
            </div>

            <div class="copyright">QuickDine &copy; {{ date('Y') }}</div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function nextStep() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            let valid = true;

            if (!name) {
                document.getElementById('error-name').classList.add('show');
                valid = false;
            } else {
                document.getElementById('error-name').classList.remove('show');
            }

            if (!email || !email.includes('@')) {
                document.getElementById('error-email').classList.add('show');
                valid = false;
            } else {
                document.getElementById('error-email').classList.remove('show');
            }

            if (valid) {
                document.getElementById('step-1').classList.add('hidden');
                document.getElementById('step-2').classList.remove('hidden');
                document.getElementById('indicator-2').classList.add('active');
            }
        }

        function prevStep() {
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('indicator-2').classList.remove('active');
        }
    </script>
</body>

</html>