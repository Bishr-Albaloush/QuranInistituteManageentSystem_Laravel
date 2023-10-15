<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Year;
use Illuminate\Http\Request;
use Validator;

class YearController extends Controller
{
    //
    public function create_year(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Year_Number',
            );

            $validator = Validator::make($data, [
                'Year_Number' => 'required|string',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $year = Year::create([
                'Year_Number' => $request->Year_Number,

            ]);

            return $this->returnData('Year', $year->where('ID_Year', $year->ID_Year)->get());

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
                'Year_Number',         
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $year = Year::find($request->id);
            $year->Year_Number = $request->Year_Number;

            return $this->returnData('Year', $year->where('ID_Year', $year->ID_Year)->get());


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_season(Request $request)
    {
        try {
            $year = Year::find($request->id);
            $result = $year->delete();
            return $this->returnSuccessMessage("year has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}