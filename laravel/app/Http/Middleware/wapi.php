<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Crypt;
use Cookie;
use App\Models\Role;

class wapi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$resources)
    {
			//if(in_array('ticket',$resources)) \Log::info("TICKET API INFO: Called with\nSegment -> " . json_encode($request->segments()) . "\nRequest -> " . json_encode($request->all()) . "\nAt -> " . date('D d/m/y h:i:s a') . "\n----------");
			$_token = $request->Cookie("api_token");
			if(isset($_token) && !empty($_token)) {
				$api_token = Crypt::decrypt($request->Cookie("api_token"));
				$api_parts = explode("|",$api_token);
				$request->merge(["api_token"=>$api_token,"_company"=>$api_parts[2],"_rolename"=>$api_parts[3]]);
				if(!Auth::guard("api")->check()) return response("Unauthorized. Auth Failed.", 401);
				if(!in_array($resources[0],["all","*"])){
					$role = explode("|",$api_token)[0];
					$roleResources = Role::find($role)->resources()->pluck("name")->toArray();
					if(empty(array_intersect($resources,$roleResources))) return response("Unauthorized. Roles doesn't allow.", 401);
				}
				return $next($request);
			} else {
				return response("Unauthorized. Empty API.", 401);
			}
    }
}
