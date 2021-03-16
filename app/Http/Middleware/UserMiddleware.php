<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class UserMiddleware
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
		//echo 'dddddd';exit;
        $user_id  =  Session::get('user_id');
        if (isset($user_id) && $user_id != ''){
        }else{
            redirect()->to('/login')->send();
        }
        return $next($request);
    }
    public function editor(){
        echo "work";
    }
}
