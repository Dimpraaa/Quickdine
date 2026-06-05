<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP QuickDine</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #F3EFE9; padding: 20px; margin: 0; color: #352214;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
        <tr>
            <td style="background-color: #B27C44; padding: 35px 20px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: 1px;">QuickDine</h1>
                <p style="color: #fceadc; margin: 5px 0 0 0; font-size: 14px;">Restaurant Management System</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 30px;">
                <h2 style="margin-top: 0; color: #352214; font-size: 22px; font-weight: 700;">Verifikasi Reset Password</h2>
                <p style="font-size: 16px; line-height: 1.6; color: #555555; margin-bottom: 30px;">
                    Halo,<br><br>
                    Kami menerima permintaan untuk melakukan pengaturan ulang (reset) password pada akun QuickDine Anda. Untuk memastikan keamanan, silakan masukkan kode OTP berikut:
                </p>
                
                <div style="text-align: center; margin: 40px 0;">
                    <div style="display: inline-block; background-color: #FDFBF7; border: 2px dashed #B27C44; border-radius: 12px; padding: 20px 40px;">
                        <span style="font-size: 38px; font-weight: 900; letter-spacing: 12px; color: #B27C44; margin-right: -12px;">{{ $otp }}</span>
                    </div>
                </div>

                <p style="font-size: 14px; color: #888888; text-align: center; margin-bottom: 30px;">
                    Mohon <strong>jangan berikan kode ini kepada siapa pun</strong>, termasuk pihak yang mengatasnamakan QuickDine.
                </p>

                <hr style="border: none; border-top: 1px solid #eeeeee; margin: 30px 0;">

                <p style="font-size: 13px; line-height: 1.6; color: #aaaaaa;">
                    <strong>Tidak merasa meminta reset password?</strong><br>
                    Jika Anda tidak merasa melakukan aktivitas ini, abaikan email ini dengan aman. Password Anda tidak akan berubah.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #FDFBF7; padding: 20px; text-align: center; border-top: 1px solid #EAE3D9;">
                <p style="margin: 0; font-size: 12px; color: #888888;">
                    &copy; {{ date('Y') }} QuickDine. Hak cipta dilindungi undang-undang.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
