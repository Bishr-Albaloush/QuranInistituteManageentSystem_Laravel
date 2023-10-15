<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Auth;
use App\Models\Permission;

class Delete_PersonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

     use GeneralTrait;
    public function handle(Request $request, Closure $next)
    {
        
        if(Auth::check()){
            $user = $request->user();
            $admin = Permission::where('ID_Permission_Pep', $user->ID_Person)->where('Delete_Person', '1')->get();
         
            $isActive = null;
            if(count($admin) != 0){    
            $isActive = $admin[0]->State;
            }
            if(count($admin)===0 || $isActive != 2){



                return $this->returnError('004','Access Denied as you are not Admin!');

            }
            else{
                return $next($request);
                
            }
        }else{
            return $this->returnError('001','Unauthenticated');
        }
       
    }
}
