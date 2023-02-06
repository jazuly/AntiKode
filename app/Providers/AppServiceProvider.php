<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ResponseFactory::macro('success', function ($data = null, $code = 200, $msg = null) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'msg' => $msg
            ], $code);
        });

        ResponseFactory::macro('error', function ($code, $msg) {
            return response()->json([
                'success' => false,
                'data' => null,
                'msg' => $msg
            ], ($code > 99 && $code < 600) ? $code : 500);
        });
    }
}
