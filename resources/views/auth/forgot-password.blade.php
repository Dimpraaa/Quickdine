<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Sandi — QuickDine</title>
    <meta name="description" content="Reset kata sandi akun QuickDine Anda melalui verifikasi email.">
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

        /* ── Brand Bar (mobile) ── */
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
        }

        .brand-name { font-weight: 800; font-size: 1rem; color: #fff; }

        /* ── Main ── */
        .main-content {
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

        /* ── Icon header ── */
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

        /* ── Alerts ── */
        .alert {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.875rem 1rem; border-radius: 12px;
            font-size: 0.8125rem; font-weight: 600; margin-bottom: 1.25rem;
        }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

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

        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; padding: 0.9375rem; font-size: 0.875rem; font-weight: 700;
            color: #fff; background: var(--secondary); border: none; border-radius: 12px;
            cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s;
            margin-top: 0.5rem;
        }
        .btn-primary:hover { background: #20150F; }
        .btn-primary:active { transform: scale(0.985); }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            margin-top: 1.5rem; text-align: center;
            font-size: 0.8125rem; font-weight: 700; color: var(--text-muted);
            text-decoration: none; transition: color 0.2s;
        }
        .back-link:hover { color: var(--secondary); }

        .copyright { text-align: center; padding: 1.5rem; font-size: 0.625rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.15em; }

        /* ── Loading Overlay ── */
        .loading-overlay {
            position: fixed; inset: 0;
            background: rgba(53,34,20,0.92);
            z-index: 50;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .loading-overlay.show {
            display: flex;
            opacity: 1;
        }

        .spinner {
            width: 48px; height: 48px;
            border: 3px solid rgba(178,124,68,0.2);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 1.25rem;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .loading-text { font-size: 0.875rem; font-weight: 700; color: #fff; letter-spacing: 0.05em; }
        .loading-sub { font-size: 0.75rem; font-weight: 500; color: var(--text-light); margin-top: 0.375rem; }

        /* ── Desktop card style ── */
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
                    <i class="fas fa-key"></i>
                </div>

                <p class="form-greeting">Pemulihan Akun</p>
                <h1 class="form-title">Lupa Kata Sandi?</h1>
                <p class="form-desc">Masukkan email yang terdaftar. Kami akan mengirimkan kode OTP untuk verifikasi.</p>

                <div id="error-alert" class="alert alert-error" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="error-text"></span>
                </div>

                <form id="forgot-form" onsubmit="processSimulate(event)">
                    <div class="field-group">
                        <label class="field-label">Email</label>
                        <div class="input-wrapper">
                            <input type="email" name="email" required
                                placeholder="nama@email.com" class="input-field" style="padding-left: 2.75rem;">
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <button type="submit" id="submit-btn" class="btn-primary">
                        Kirim Kode OTP <i class="fas fa-paper-plane" style="font-size: 0.75rem;"></i>
                    </button>
                </form>

                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>

        <div class="copyright">QuickDine &copy; {{ date('Y') }}</div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="spinner"></div>
        <p class="loading-text">Memverifikasi</p>
        <p class="loading-sub">Mengirim kode OTP ke email Anda...</p>
    </div>

    <script>
        const urlSimulate = "{{ route('password.simulate') }}";
        const urlOtp = "{{ route('password.verify_otp_form') }}";
        const csrfToken = "{{ csrf_token() }}";

        async function processSimulate(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('submit-btn');
            const errorAlert = document.getElementById('error-alert');
            const errorText = document.getElementById('error-text');
            const overlay = document.getElementById('loading-overlay');

            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
            btn.disabled = true;
            errorAlert.style.display = 'none';

            try {
                const res = await fetch(urlSimulate, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ email: form.email.value })
                });
                const data = await res.json();

                if (!res.ok) {
                    btn.innerHTML = 'Kirim Kode OTP <i class="fas fa-paper-plane" style="font-size: 0.75rem;"></i>';
                    btn.disabled = false;
                    errorText.innerText = data.errors ? data.errors.email[0] : data.message;
                    errorAlert.style.display = 'flex';
                    return;
                }

                overlay.classList.add('show');

                setTimeout(() => {
                    window.location.href = urlOtp;
                }, 1500);

            } catch (err) {
                btn.innerHTML = 'Kirim Kode OTP <i class="fas fa-paper-plane" style="font-size: 0.75rem;"></i>';
                btn.disabled = false;
                errorText.innerText = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                errorAlert.style.display = 'flex';
            }
        }
    </script>
</body>

</html>