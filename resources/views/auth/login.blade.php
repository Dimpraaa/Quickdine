<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — QuickDine</title>
    <meta name="description" content="Masuk ke akun QuickDine Anda untuk memesan makanan dengan cepat dan mudah.">
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

        /* ── Layout ── */
        .auth-wrapper {
            display: flex;
            min-height: 100vh;
        }

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
            background: url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80') center/cover;
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
            width: 56px;
            height: 56px;
            background: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.75rem;
            color: #fff;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 30px rgba(178,124,68,0.3);
        }

        .side-title {
            font-size: 2.75rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .side-subtitle {
            font-size: 1rem;
            color: var(--text-light);
            line-height: 1.6;
            max-width: 380px;
            font-weight: 500;
        }

        .side-accent {
            width: 48px;
            height: 4px;
            background: var(--primary);
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        /* ── Main Panel ── */
        .main-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--bg-warm);
        }

        /* ── Mobile Brand Bar ── */
        .brand-bar {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--secondary);
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-name {
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            letter-spacing: -0.01em;
        }

        /* ── Form Container ── */
        .form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
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

        .form-header {
            margin-bottom: 2rem;
        }

        .form-greeting {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.5rem;
        }

        .form-title {
            font-size: 1.625rem;
            font-weight: 900;
            color: var(--secondary);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }

        .form-desc {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
            line-height: 1.5;
        }

        /* ── Alerts ── */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ── Form Elements ── */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }

        .field-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .forgot-link {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.875rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-field {
            width: 100%;
            padding: 0.875rem 0.875rem 0.875rem 2.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--secondary);
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .input-field::placeholder {
            color: var(--text-light);
            font-weight: 400;
        }

        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(178,124,68,0.1);
        }

        .input-field:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .input-field-password {
            padding-right: 3rem;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 0.875rem;
            padding: 4px;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        /* ── Button ── */
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.9375rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #fff;
            background: var(--secondary);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.01em;
        }

        .btn-primary:hover {
            background: #20150F;
        }

        .btn-primary:active {
            transform: scale(0.985);
        }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider-text {
            font-size: 0.6875rem;
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Footer ── */
        .auth-footer {
            text-align: center;
            font-size: 0.8125rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .auth-footer a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-footer a:hover {
            color: var(--primary-dark);
        }

        .copyright {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.625rem;
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        /* ── Desktop responsive ── */
        @media (min-width: 1024px) {
            .side-panel {
                display: flex;
            }
            .brand-bar {
                display: none;
            }
            .form-container {
                padding: 3rem;
            }
            .form-title {
                font-size: 1.875rem;
            }
            .copyright {
                display: none;
            }
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
                <h1 class="side-title">Pesan Makanan<br>Tanpa Antrean.</h1>
                <p class="side-subtitle">Sistem pemesanan cerdas QuickDine. Nikmati hidangan favorit Anda dengan lebih cepat, langsung dari meja Anda.</p>
            </div>
        </div>

        <!-- Main Panel -->
        <div class="main-panel">
            <!-- Mobile Brand Bar -->
            <div class="brand-bar">
                <div class="brand-logo">Q</div>
                <span class="brand-name">QuickDine</span>
            </div>

            <!-- Form -->
            <div class="form-container">
                <div class="form-card">
                    <div class="form-header">
                        <p class="form-greeting">Selamat Datang Kembali</p>
                        <h1 class="form-title">Masuk ke Akun</h1>
                        <p class="form-desc">Masuk untuk melihat riwayat pesanan dan nikmati kemudahan memesan.</p>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf

                        <div class="field-group">
                            <label for="email" class="field-label">Email</label>
                            <div class="input-wrapper">
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                    placeholder="nama@email.com" class="input-field" style="padding-left: 2.75rem;">
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                        </div>

                        <div class="field-group">
                            <div class="field-label-row">
                                <label for="password" class="field-label" style="margin-bottom:0;">Kata Sandi</label>
                                <a href="{{ route('password.request') }}" class="forgot-link">Lupa sandi?</a>
                            </div>
                            <div class="input-wrapper">
                                <input type="password" id="password" name="password" required
                                    placeholder="Masukkan kata sandi" class="input-field input-field-password" style="padding-left: 2.75rem;">
                                <i class="fas fa-lock input-icon"></i>
                                <button type="button" onclick="togglePassword('password', 'eye-login')" class="toggle-password" aria-label="Tampilkan kata sandi">
                                    <i id="eye-login" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary" id="btn-login">
                            Masuk <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                        </button>
                    </form>

                    <div class="divider">
                        <div class="divider-line"></div>
                        <span class="divider-text">atau</span>
                        <div class="divider-line"></div>
                    </div>

                    <p class="auth-footer">
                        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
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
    </script>
</body>

</html>