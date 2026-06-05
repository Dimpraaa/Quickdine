<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP — QuickDine</title>
    <meta name="description" content="Verifikasi kode OTP untuk melanjutkan reset kata sandi QuickDine Anda.">
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
            text-align: center;
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
            margin: 0 auto 1.5rem;
        }

        .icon-header i { font-size: 1.25rem; color: var(--primary); }

        .form-greeting { font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
        .form-title { font-size: 1.625rem; font-weight: 900; color: var(--secondary); letter-spacing: -0.02em; line-height: 1.2; margin-bottom: 0.5rem; }
        .form-desc { font-size: 0.875rem; color: var(--text-muted); font-weight: 500; line-height: 1.5; margin-bottom: 2rem; }
        .form-desc .email-highlight { color: var(--primary); font-weight: 700; }

        /* ── Alert ── */
        .alert {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.875rem 1rem; border-radius: 12px;
            font-size: 0.8125rem; font-weight: 600; margin-bottom: 1.25rem;
            text-align: left;
        }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; display: none; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; display: none; }

        /* ── OTP inputs ── */
        .otp-group {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .otp-input {
            width: 3.25rem;
            height: 4rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            border-radius: 12px;
            border: 2px solid var(--border);
            background: #fff;
            color: var(--secondary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
            font-family: inherit;
            caret-color: var(--primary);
        }

        .otp-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(178,124,68,0.12);
            transform: scale(1.04);
        }

        /* ── Buttons ── */
        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; padding: 0.9375rem; font-size: 0.875rem; font-weight: 700;
            color: #fff; background: var(--secondary); border: none; border-radius: 12px;
            cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s;
        }
        .btn-primary:hover { background: #20150F; }
        .btn-primary:active { transform: scale(0.985); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-primary.success { background: #16a34a; }

        .resend-section {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .resend-btn {
            background: none; border: none; cursor: pointer;
            color: var(--primary); font-weight: 700;
            font-family: inherit; font-size: 0.75rem;
            transition: color 0.2s;
        }
        .resend-btn:hover { color: var(--primary-dark); }
        .resend-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            margin-top: 1.25rem;
            font-size: 0.8125rem; font-weight: 700; color: var(--text-muted);
            text-decoration: none; transition: color 0.2s;
        }
        .back-link:hover { color: var(--secondary); }

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

                <p class="form-greeting">Verifikasi Keamanan</p>
                <h1 class="form-title">Masukkan Kode OTP</h1>
                <p class="form-desc">Kode 4 digit telah dikirim ke<br><span class="email-highlight">{{ $email }}</span></p>

                <div id="error-alert" class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="error-text"></span>
                </div>

                <div id="success-alert" class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span id="success-text"></span>
                </div>

                <form id="otp-form" onsubmit="processOtp(event)">
                    <div class="otp-group">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input" pattern="[0-9]" required oninput="focusNext(this, 'otp-2')" id="otp-1" autofocus>
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input" pattern="[0-9]" required oninput="focusNext(this, 'otp-3')" id="otp-2">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input" pattern="[0-9]" required oninput="focusNext(this, 'otp-4')" id="otp-3">
                        <input type="text" inputmode="numeric" maxlength="1" class="otp-input" pattern="[0-9]" required oninput="focusNext(this, null)" id="otp-4">
                    </div>

                    <button type="submit" id="verify-btn" class="btn-primary">
                        Verifikasi <i class="fas fa-check-circle" style="font-size: 0.75rem;"></i>
                    </button>
                </form>

                <div class="resend-section">
                    Belum menerima kode? <button type="button" onclick="resendOtp()" id="resend-btn" class="resend-btn">Kirim Ulang</button>
                </div>

                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>

        <div class="copyright">QuickDine &copy; {{ date('Y') }}</div>
    </div>

    <script>
        const urlVerify = "{{ route('password.verify_otp') }}";
        const urlReset = "{{ route('password.reset') }}";
        const urlResend = "{{ route('password.resend_otp') }}";
        const csrfToken = "{{ csrf_token() }}";

        function focusNext(elem, nextId) {
            if (elem.value.length === 1 && nextId) {
                document.getElementById(nextId).focus();
            }
        }

        // Handle backspace navigation
        document.querySelectorAll('.otp-input').forEach((input, index, arr) => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    arr[index - 1].focus();
                }
            });
        });

        async function resendOtp() {
            const btn = document.getElementById('resend-btn');
            const errorAlert = document.getElementById('error-alert');
            const errorText = document.getElementById('error-text');
            const successAlert = document.getElementById('success-alert');
            const successText = document.getElementById('success-text');

            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengirim...';
            btn.disabled = true;
            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';

            try {
                const res = await fetch(urlResend, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();

                if (!res.ok) {
                    btn.innerHTML = 'Kirim Ulang';
                    btn.disabled = false;
                    errorText.innerText = data.message || 'Gagal mengirim ulang kode.';
                    errorAlert.style.display = 'flex';
                    return;
                }

                btn.innerHTML = 'Terkirim <i class="fas fa-check"></i>';
                successText.innerText = data.message || 'OTP berhasil dikirim ulang ke email Anda.';
                successAlert.style.display = 'flex';

                setTimeout(() => {
                    btn.innerHTML = 'Kirim Ulang';
                    btn.disabled = false;
                }, 5000);

            } catch (err) {
                btn.innerHTML = 'Kirim Ulang';
                btn.disabled = false;
                errorText.innerText = 'Terjadi kesalahan jaringan saat mengirim ulang.';
                errorAlert.style.display = 'flex';
            }
        }

        async function processOtp(e) {
            e.preventDefault();
            const btn = document.getElementById('verify-btn');
            const errorAlert = document.getElementById('error-alert');
            const errorText = document.getElementById('error-text');
            const successAlert = document.getElementById('success-alert');

            const otpValue = document.getElementById('otp-1').value +
                             document.getElementById('otp-2').value +
                             document.getElementById('otp-3').value +
                             document.getElementById('otp-4').value;

            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memverifikasi...';
            btn.disabled = true;
            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';

            try {
                const res = await fetch(urlVerify, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ otp: otpValue })
                });
                const data = await res.json();

                if (!res.ok) {
                    btn.innerHTML = 'Verifikasi <i class="fas fa-check-circle" style="font-size: 0.75rem;"></i>';
                    btn.disabled = false;
                    errorText.innerText = data.errors ? data.errors.otp[0] : data.message;
                    errorAlert.style.display = 'flex';

                    document.querySelectorAll('.otp-input').forEach(input => input.value = '');
                    document.getElementById('otp-1').focus();
                    return;
                }

                btn.classList.add('success');
                btn.innerHTML = '<i class="fas fa-check"></i> Terverifikasi';

                setTimeout(() => {
                    window.location.href = urlReset;
                }, 1000);

            } catch (err) {
                btn.innerHTML = 'Verifikasi <i class="fas fa-check-circle" style="font-size: 0.75rem;"></i>';
                btn.disabled = false;
                errorText.innerText = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                errorAlert.style.display = 'flex';
            }
        }
    </script>
</body>

</html>
