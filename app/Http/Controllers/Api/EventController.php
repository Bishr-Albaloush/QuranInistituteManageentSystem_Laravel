<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Event;
use Illuminate\Http\Request;
use Validator;

class EventController extends Controller
{
    //
    public function create_event(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Event_Name',
                'Mistake_Point_Percent',
                'Rate_Point_Percent',
                'Season',
                'State',
                
            );

            $validator = Validator::make($data, [
                'Event_Name' => 'required|string',
                'State' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            

            $rate = Event::create([
                'Event_Name' => $request->Rate_Name,
                'Mistake_Point_Percent' => $request->Mistake_Point_Percent,
                'Rate_Point_Percent' => $request->Rate_Point_Percent,
                'Season' => $request->Season,
                'State' => $request->State,
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
                'Event_Name',
                'Mistake_Point_Percent',
                'Rate_Point_Percent',
                'Season',
                'State',
                
            );

            $validator = Validator::make($data, [
                'id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            
            $event = Event::find($request->id);
            $event->Event_Name = $request->Event_Name;
            $event->Mistake_Point_Percent = $request->Mistake_Point_Percent;
            $event->Rate_Point_Percent = $request->Rate_Point_Percent;
            $event->Season = $request->Season;
            $event->State = $request->State; 
        

            return $this->returnData('event', $event->where('ID_Event', $event->ID_Event)->get());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_rate(Request $request)
    {
        try {
            $event = Event::find($request->id);
            $result = $event->delete();
            return $this->returnSuccessMessage("event has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
