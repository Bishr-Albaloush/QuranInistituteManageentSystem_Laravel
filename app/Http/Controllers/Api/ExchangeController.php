<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Exchange;
use Illuminate\Http\Request;
use Validator;

class ExchangeController extends Controller
{
    //
    public function create_exchange(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Season',
                'Price',
                'Value',
                
            );

            $validator = Validator::make($data, [
                'Price' => 'required|string',
                'Value' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $exchange = Event::create([
                'Season' => $request->Season,
                'Price' => $request->Price,
                'Value' => $request->Value,

            ]);

            return $this->returnData('Exchange', $exchange->where('ID_Exchange', $exchange->ID_Exchange)->get());

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
                'Season',
                'Price',
                'Value',
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $exchange = Exchange::find($request->id);
            $exchange->Season = $request->Season;
            $exchange->Price = $request->Price;
            $exchange->Value = $request->Value;

        

            return $this->returnData('exchange', $exchange->where('ID_Exchange', $exchange->ID_Exchange)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_rate(Request $request)
    {
        try {
            $exchange = Exchange::find($request->id);
            $result = $exchange->delete();
            return $this->returnSuccessMessage("exchange has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
