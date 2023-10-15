<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\State;
use Illuminate\Http\Request;
use Validator;

class StateController extends Controller
{
    //
    public function create_state(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'State_Name',
            );

            $validator = Validator::make($data, [
                'State_Name' => 'required|string',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $state = State::create([
                'State_Name' => $request->State_Name,

            ]);

            return $this->returnData('State', $state->where('ID_State', $state->ID_State)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_exchange(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'id',
                'State_Name',         
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $state = State::find($request->id);
            $state->State_Name = $request->State_Name;

            return $this->returnData('State', $state->where('ID_State', $state->ID_State)->get());


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_season(Request $request)
    {
        try {
            $state = State::find($request->id);
            $result = $state->delete();
            return $this->returnSuccessMessage("state has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}