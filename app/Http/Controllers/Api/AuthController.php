<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Groups_has_Assistant;
use App\Models\Permission;

use App\Models\User;
use App\Traits\GeneralTrait;
use DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Auth;

class AuthController extends Controller
{

    use GeneralTrait;


    public function login(Request $request)
    {

        try {
            $rules = [
                "UserName" => "required",
                "password" => "required"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            //login
            $credentials = $request->only(['UserName', 'password']);

            $token = Auth::guard('user-api')->attempt($credentials); //generate token

            if (!$token)
                return $this->returnError('E001', 'Credentials is not correct');

            $user = Auth::guard('user-api')->user();
       
            $permission = Permission::find($user->ID_Person);
            if ($permission != null) {
                $permission = $permission->with(['supervisor_groups', 'moderator_groups', 'state'])->where("ID_Permission_Pep", $user->ID_Person)->first();
                $assistant_groups = Groups_has_Assistant::where('Assistants', $user->ID_Person);
                if ($assistant_groups) {
                    $assistant_groups = $assistant_groups->with(['groups'])->get();
                    $real_assistant_groups = [];
                    foreach ($assistant_groups as $i) {
                        array_push($real_assistant_groups, $i->groups);
                    }
                    $permission->assistant_groups = $real_assistant_groups;
                }
            }


            $user = $user->where('ID_Person', $user->ID_Person)->with([
                'call_phone',
                'social_phone',
                'mother' => function ($qq) {
                    $qq->with(['phone']);
                    $qq->with(['job']);
                    $qq->with(['state']);

                },
                'father' => function ($qq) {
                    $qq->with(['phone']);
                    $qq->with(['job']);
                    $qq->with(['state']);


                },
                'kin' => function ($qq) {
                    $qq->with(['phone']);
                    $qq->with(['job']);
                    $qq->with(['state']);
                },
                'address',
                'job',
                'education' => function ($qq) {
                    $qq->with(['major']);
                },
                'state',
                'student' => function ($qq) {
                    $qq->with(['group']);
                    $qq->with(['state']);
                },


            ])->first();
            $old_token = $user->Token;
            $user->Token = $token;
            $user->save();
            $user->permission = $permission;
            $user->api_token = $token;
            if ($old_token) {
                try {

                    JWTAuth::setToken($old_token)->invalidate(true); //logout
                } catch (\Tymon\JWTAuth\Throwables\TokenInvalidThrowable $e) {
                    return $this->returnError('', 'some thing went wrongs');
                }
            }
            else{
                return $this->returnError('', 'يرجى إعادة تسجيل الدخول');
            }
            //return token
            return $this->returnData('user', $user); //return json response

        } catch (Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->header('auth-token');
            if ($token) {
                try {

                    JWTAuth::setToken($token)->invalidate(); //logout
                } catch (\Tymon\JWTAuth\Throwables\TokenInvalidThrowable $e) {
                    return $this->returnError('', 'some thing went wrongs');
                }
                return $this->returnSuccessMessage('Logged out successfully');
            } else {
                $this->returnError('', 'some thing went wrongs');
            }
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }
    public function empty_tokens(Request $request){
        try{
       
        $Permission = Permission::all();
        foreach($Permission as $i){
            $i->Edit_Person = 1;
            $i->save();
        }
        }
    
 catch (\Throwable $ex) {
    return $this->returnError($ex->getCode(), $ex->getMessage());
}
    }}