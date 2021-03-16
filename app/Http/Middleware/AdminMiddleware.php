<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $admin_id  =  Session::get('admin_id');
        if (isset($admin_id) && $admin_id != ''){
        }else{
            redirect()->to('/admin')->send();
        }
        return $next($request);
    }
    public function editor(){
        echo "work";
    }
}
