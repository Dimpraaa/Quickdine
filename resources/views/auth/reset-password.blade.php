<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Sandi Baru — QuickDine</title>
    <meta name="description" content="Buat kata sandi baru untuk akun QuickDine Anda.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #B27C44;
            --primary-dark: #8B5A2B;
            --secondary: #352214;
            --bg-warm: #FDFBF7;
            --text-muted: #8C837C;
            --text-light: #A69C94;
            --border: #EAE3D9;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-warm);
            color: var(--secondary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .auth-wrapper { display: flex; flex-direction: column; min-height: 100vh; }

        .brand-bar {
            padding: 1rem 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
            background: var(--secondary);
        }

        .brand-logo {
            width: 36px; height: 36px;
            background: var(--primary); border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.1rem; color: #fff;
        }

        .brand-name { font-weight: 800; font-size: 1rem; color: #fff; }

        .main-content {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem 1.5rem;
        }

        .form-card {
            width: 100%; max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-header {
            width: 56px; height: 56px;
            background: var(--bg-warm);
            border: 2px solid var(--border);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
        }

        .icon-header i { font-size: 1.25rem; color: var(--primary); }

        .form-greeting { font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
        .form-title { font-size: 1.625rem; font-weight: 900; color: var(--secondary); letter-spacing: -0.02em; line-height: 1.2; margin-bottom: 0.5rem; }
        .form-desc { font-size: 0.875rem; color: var(--text-muted); font-weight: 500; line-height: 1.5; margin-bottom: 2rem; }

        /* ── Alert ── */
        .alert {
            display: flex; align-items: flex-start; gap: 0.75rem;
            padding: 0.875rem 1rem; border-radius: 12px;
            font-size: 0.8125rem; font-weight: 600; margin-bottom: 1.25rem;
        }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert i { margin-top: 2px; flex-shrink: 0; }
        .alert ul { list-style: disc; padding-left: 1rem; margin: 0; }
        .alert ul li + li { margin-top: 0.25rem; }

        /* ── Form ── */
        .field-group { margin-bottom: 1.25rem; }
        .field-label { display: block; font-size: 0.8125rem; font-weight: 700; color: var(--secondary); margin-bottom: 0.5rem; }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--text-light); font-size: 0.875rem; pointer-events: none; transition: color 0.2s;
        }

        .input-field {
            width: 100%;
            padding: 0.875rem 0.875rem 0.875rem 2.75rem;
            font-size: 0.875rem; font-weight: 500; color: var(--secondary);
            background: #fff; border: 1.5px solid var(--border); border-radius: 12px;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit;
        }
        .input-field::placeholder { color: var(--text-light); font-weight: 400; }
        .input-field:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(178,124,68,0.1); }
        .input-wrapper:focus-within .input-icon { color: var(--primary); }
        .input-field-password { padding-right: 3rem; }

        .input-field-readonly {
            background: var(--bg-warm);
            cursor: not-allowed;
            opacity: 0.7;
            font-weight: 700;
        }

        .toggle-password {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--text-light);
            cursor: pointer; font-size: 0.875rem; padding: 4px; transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--primary); }

        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; padding: 0.9375rem; font-size: 0.875rem; font-weight: 700;
            color: #fff; background: var(--secondary); border: none; border-radius: 12px;
            cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s;
            margin-top: 0.5rem;
        }
        .btn-primary:hover { background: #20150F; }
        .btn-primary:active { transform: scale(0.985); }

        .copyright { text-align: center; padding: 1.5rem; font-size: 0.625rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.15em; }

        @media (min-width: 1024px) {
            .brand-bar { display: none; }
            .main-content { background: #f5f0e8; }
            .form-card {
                background: #fff;
                padding: 2.5rem;
                border-radius: 24px;
                border: 1px solid var(--border);
                box-shadow: 0 4px 24px rgba(53,34,20,0.06);
            }
            .copyright { display: none; }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="brand-bar">
            <div class="brand-logo">Q</div>
            <span class="brand-name">QuickDine</span>
        </div>

        <div class="main-content">
            <div class="form-card">
                <div class="icon-header">
                    <i class="fas fa-shield-halved"></i>
                </div>

                <p class="form-greeting">Langkah Terakhir</p>
                <h1 class="form-title">Buat Kata Sandi Baru</h1>
                <p class="form-desc">Buat kata sandi baru yang kuat untuk mengamankan akun Anda.</p>

                @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <div class="field-group">
                        <label class="field-label">Akun Email</label>
                        <div class="input-wrapper">
                            <input type="email" name="email" value="{{ $email }}" readonly
                                class="input-field input-field-readonly" style="padding-left: 2.75rem;">
                            <i class="fas fa-user-check input-icon" style="color: #16a34a;"></i>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Kata Sandi Baru</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" required minlength="6"
                                placeholder="Minimal 6 karakter" class="input-field input-field-password" style="padding-left: 2.75rem;">
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" onclick="togglePassword('password', this)" class="toggle-password" aria-label="Tampilkan kata sandi">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Konfirmasi Kata Sandi</label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6"
                                placeholder="Ketik ulang kata sandi baru" class="input-field input-field-password" style="padding-left: 2.75rem;">
                            <i class="fas fa-check-double input-icon"></i>
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="toggle-password" aria-label="Tampilkan konfirmasi kata sandi">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save" style="font-size: 0.75rem;"></i> Simpan & Login
                    </button>
                </form>
            </div>
        </div>

        <div class="copyright">QuickDine &copy; {{ date('Y') }}</div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');

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