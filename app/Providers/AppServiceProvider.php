<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Paginator::useBootstrapFour();

        View::composer('*', function ($view) {

            $customerId = session('customer_id');
            $customer = null;
    
            if ($customerId) {
                $customer = User::find($customerId);
            }
    
            $view->with('customer', $customer);
        });
    }
}
