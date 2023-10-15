<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Point;
use Illuminate\Http\Request;
use Validator;
class PointController extends Controller
{
    //
    public function create_point(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Exchanges_ID_Exchange',
                
            );

            $validator = Validator::make($data, [
                'Exchanges_ID_Exchange' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $point = Point::create([

                'Events_ID_Event' => $request->Events_ID_Event,
                
            ]);

            return $this->returnData('Rate', $point->where('ID_Point', $point->ID_Point)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_point(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'id',
                'Exchanges_ID_Exchange',
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
                'Exchanges_ID_Exchange' => 'required',

            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $point = Point::find($request->id);

            $point->Exchanges_ID_Exchange = $request->Exchanges_ID_Exchange;
                
            

            return $this->returnData('point', $point->where('ID_Point', $point->ID_Point)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_point(Request $request)
    {
        try {
            $point = Point::find($request->id);
            $result = $point->delete();
            return $this->returnSuccessMessage("point has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
