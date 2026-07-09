<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Mail::extend('brevo', function (array $config) {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                env('MAIL_HOST', 'smtp-relay.brevo.com'),
                (int) env('MAIL_PORT', 587),
                null // auto-detect: STARTTLS untuk port 587
            );
            $transport->setUsername(env('MAIL_USERNAME'));
            $transport->setPassword(env('MAIL_PASSWORD'));
            return $transport;
        });

        Paginator::useTailwind();

        // Setup Midtrans Configuration globally
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }
}
