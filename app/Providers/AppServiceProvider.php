<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Seluruh antarmuka menggunakan bahasa Indonesia.
        Carbon::setLocale('id');
        Date::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        Paginator::defaultView('vendor.pagination.wbs');
        Paginator::defaultSimpleView('vendor.pagination.wbs');
    }
}
