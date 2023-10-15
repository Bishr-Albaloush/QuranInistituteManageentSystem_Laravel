<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Rate;
use Illuminate\Http\Request;
use Validator;

class RateController extends Controller
{
    //
    public function create_rate(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Rate_Name',
                'Point_Count',
                'Events_ID_Event',
                
            );

            $validator = Validator::make($data, [
                'Rate_Name' => 'required|string',
                'Point_Count' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $rate = Rate::create([
                'Rate_Name' => $request->Rate_Name,
                'Point_Count' => $request->Point_Count,
                'Events_ID_Event' => $request->Events_ID_Event,
                
            ]);

            return $this->returnData('Rate', $rate->where('ID_Rate', $rate->ID_Rate)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_rate(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'id',
                'Rate_Name',
                'Point_Count',
                'Events_ID_Event',
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
                'Rate_Name' => 'required|string',
                'Point_Count' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $rate = Rate::find($request->id);
            $rate->Rate_Name = $request->Rate_Name;
            $rate->Point_Count = $request->Point_Count;
            $rate->Events_ID_Event = $request->Events_ID_Event;
                
            

            return $this->returnData('rate', $rate->where('ID_Rate', $rate->ID_Rate)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_rate(Request $request)
    {
        try {
            $rate = Rate::find($request->id);
            $result = $rate->delete();
            return $this->returnSuccessMessage("rate has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
