<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use App\Services\CustomCaptcha;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository;
use Intervention\Image\ImageManager;
use Illuminate\Session\Store;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('captcha', function ($app) {
            return new CustomCaptcha(
                $app->make(Filesystem::class),
                $app->make(Repository::class),
                $app->make(ImageManager::class),
                $app->make(Store::class),
                $app->make(Hasher::class),
                $app->make(Str::class)
            );
        });
    }



    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // http scheme forced for local system for running in development environment by Adarsh
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
        Mail::extend('smtp-no-enc', function () {
            $transport = new EsmtpTransport(
                host: 'otprelay.nic.in',
                port: 465,
                encryption: null,   // disable SSL/TLS
            );
            $transport->setUsername('noreply-edharti@gov.in');
            $transport->setPassword('eDharti@140294@');
            return new \Illuminate\Mail\Transport\SmtpTransport($transport);
        });
    }
}
