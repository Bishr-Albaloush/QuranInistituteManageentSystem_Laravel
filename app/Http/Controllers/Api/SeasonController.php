<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Season;
use Illuminate\Http\Request;
use Validator;

class SeasonController extends Controller
{
    //
    public function create_season(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Season_Name',
                'Year_ID_Year',                
            );

            $validator = Validator::make($data, [
                'Season_Name' => 'required|string',
                'Year_ID_Year' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $season = Season::create([
                'Season_Name' => $request->Season_Name,
                'Year_ID_Year' => $request->Year_ID_Year,

            ]);

            return $this->returnData('Season', $season->where('ID_Season', $season->ID_Season)->get());

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
                'Season_Name',
                'Year_ID_Year',               
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $season = Season::find($request->id);
            $season->Season_Name = $request->Season_Name;
            $season->Year_ID_Year = $request->Year_ID_Year;

        

            return $this->returnData('season', $season->where('ID_Season', $season->ID_Season)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_season(Request $request)
    {
        try {
            $season = Season::find($request->id);
            $result = $season->delete();
            return $this->returnSuccessMessage("season has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}