<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use App\Models\Role;


class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $RequestResource)
    {
			if(!Auth::check()) return redirect()->route("login");
			if(($request->user()->roles()->count() > 1) && !session("_role")){
				session()->put('_after_roleselect',$request->url());
				return redirect()->route("roleselect");
			}
			if(in_array(session("_role"),$request->user()->roles()->pluck("code")->toArray())){
				$Role = new Role();
				$Resources = $Role->find(session("_role"))->resources()->pluck("name")->toArray();
				if(in_array($RequestResource,$Resources)){
                    error_reporting(E_ERROR & ~E_WARNING);
                    return $next($request);
                }
			}
			return redirect()->route("roledenied");
    }
}
