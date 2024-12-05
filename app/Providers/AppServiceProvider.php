<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
 
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
   
    public function register(): void {}

    public function boot(): void
    {
         View::composer('*', function ($view) {
            $user = Auth::user();
             if ($user) {
                 $view->with('authUser', $user);
             }
         });
    }
}
