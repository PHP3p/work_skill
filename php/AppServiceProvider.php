<?php

namespace App\Providers;

use App\Models\Redis\RedisSessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        \URL::forceScheme('https');
//        Auth::extend('RedisSessionGuard', function(){
//            return new RedisSessionGuard('web', Auth::user(), Session::class, Request::class);   //返回自定义 guard 实例
//        });

        DB::listen(function($query) {
            $tmp = str_replace('?', "'".'%s'."'", $query->sql);
            $tmp = vsprintf($tmp, $query->bindings);
            $tmp = str_replace("\\","",$tmp);

              // Log::info("sql_all日志".$tmp);
              echo $tmp,PHP_EOL,PHP_EOL;
        });
        Auth::provider('RedisUserProvider', function(){
            return new RedisUserProvider();    // 返回自定义的 user provider
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
