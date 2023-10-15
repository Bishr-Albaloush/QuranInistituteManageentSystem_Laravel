<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Mistake;
use Illuminate\Http\Request;
use Validator;

class MistakesController extends Controller
{
    //
    public function create_mistake_type(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Mistake_Type',
                'Point_Count',
                'Events_ID_Event',
                
            );

            $validator = Validator::make($data, [
                'Mistake_Type' => 'required|string',
                'Point_Count' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $mistake = Mistake::create([
                'Mistake_Type' => $request->Mistake_Type,
                'Point_Count' => $request->Point_Count,
                'Events_ID_Event' => $request->Events_ID_Event,
                
            ]);

            return $this->returnData('mistake', $mistake->where('ID_Mistake', $mistake->ID_Mistake)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_mistake_type(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'id',
                'Mistake_Type',
                'Point_Count',
                'Events_ID_Event',
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
                'Mistake_Type' => 'required|string',
                'Point_Count' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $mistake = Mistake::find($request->id);
            $mistake->Mistake_Type = $request->Mistake_Type;
            $mistake->Point_Count = $request->Point_Count;
            $mistake->Events_ID_Event = $request->Events_ID_Event;
                
            

            return $this->returnData('mistake', $mistake->where('ID_Mistake', $mistake->ID_Mistake)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_mistake(Request $request)
    {
        try {
            $mistake = Mistake::find($request->id);
            $result = $mistake->delete();
            return $this->returnSuccessMessage("mistake has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
