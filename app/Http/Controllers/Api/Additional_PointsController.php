<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Additional_Poit;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Student;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;

class Additional_PointsController extends Controller
{
    //
    use GeneralTrait;
    public function add_additional_points(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Note',
                'Points',
                'Receiver_Pep',
                'Sender_Per',
            
            );

            $validator = Validator::make($data, [
                'Receiver_Pep' => 'required',
                'Sender_Per' => 'required',
                'Points' => 'required',
            ]);
            $user = $request->user();
            $adder = Permission::find($user->ID_Person);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            if ($adder->Admin != 1 && $adder->Supervisor != 1 && $adder->Manager != 1) {

                $student = Student::find($request->Receiver_Pep["ID_Person"]);
                $group = Group::find($student->Group);

                if ($group != null) {
                    if ($group->Moderator != $adder->ID_Permission_Pep) {

                        return $this->returnError("102", "لا تملك الصلاحيات الكافية");
                    }
                }

            }
            $additional = Additional_Poit::where('Receiver_Pep', $request->Receiver_Pep['ID_Person'])->where('Sender_Per', $request->Sender_Per['ID_Person'])->where('created_at', date("Y-m-d"))->get();

            $sum = 0;
            foreach ($additional as $i) {
                $sum += $i->Points;
            }

            if ($sum + $request->Points <= 25 || $adder->Admin == 1 || $adder->Manager == 1) {

                $additional = Additional_Poit::create([
                    'Note' => $request->Note,
                    'Points' => $request->Points,
                    'Sender_Per' => $request->Sender_Per['ID_Person'],
                    'Receiver_Pep' => $request->Receiver_Pep['ID_Person'],

                ]);
            } else {
                return $this->returnError('E200', 'لا يمكنك إضافة أكثر من 25 نقطة في اليوم الواحد');
            }


            $reciver = User::find($request->Receiver_Pep['ID_Person']);
            $reciver->Temp_Points += $request->Points;
            $reciver->save();
            return $this->returnData('ID_Additional_Points', $additional->ID_Additional_Points);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function sell(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(

                'Points',
                'Receiver_Pep',
                'Sender_Per',

            );

            $validator = Validator::make($data, [
                'Receiver_Pep' => 'required',
                'Sender_Per' => 'required',
                'Points' => 'required',
            ]);
            $user = $request->user();
            $adder = Permission::find($user->ID_Person);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }


            $additional = Additional_Poit::where('Receiver_Pep', $request->Receiver_Pep['ID_Person'])->where('Sender_Per', $request->Sender_Per['ID_Person'])->where('created_at', date("Y-m-d"))->get();

            if ($additional != null) {
                if ($adder->Admin) {
                    $additional->Note = $request->Note;
                    $additional->Points += $request->Points;
                    $additional->save();
                }
                return $this->returnError('E200', 'ليس لديك الصلاحيات المناسبة');
            } else {
                if ($adder->Seller) {

                    $additional = Additional_Poit::create([
                        'Note' => "عملية بيع",
                        'Points' => $request->Points,
                        'Receiver_Pep' => $request->Receiver_Pep,
                        'Sender_Per' => $request->Sender_Per,

                    ]);
                } else {
                    return $this->returnError('E200', 'ليس لديك الصلاحيات المناسبة');

                }
            }
            $reciver = User::find($request->Receiver_Pep);
            $reciver->Temp_Points += $request->Points;
            $reciver->save();
            return $this->returnSuccessMessage('adding points done successfully');

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_additional_points(Request $request)
    {
        $data = $request->only(
            'ID_Additional_Points',
            'Note',
            'Points',
            'Receiver_Pep',
            'Sender_Per',

        );

        $validator = Validator::make($data, [
            'ID_Additional_Points' => 'required',
            'Sender_Per' => 'required',
            'Receiver_Pep' => 'required',
            'Points' => 'required',
        ]);
        $user = $request->user();
        $adder = Permission::find($user->ID_Person);
        $additional = Additional_Poit::find($request->ID_Additional_Points);

        if ($adder->Admin != 1 && $adder->Supervisor != 1 && $adder->Manager != 1) {

            $student = Student::find($request->Receiver_Pep["ID_Person"]);
            $group = Group::find($student->Group);

            if ($group != null) {
                if ($group->Moderator != $adder->ID_Person) {

                    return $this->returnError("102", "لا تملك الصلاحيات الكافية");
                }
            }

        }
        if ($additional != null) {

            $additionals = Additional_Poit::where('Receiver_Pep', $request->Receiver_Pep['ID_Person'])->where('Sender_Per', $request->Sender_Per['ID_Person'])->where('created_at', date("Y-m-d", strtotime($additional->created_at)))->get();

            $sum = 0;
            foreach ($additionals as $i) {
                $sum += $i->Points;
            }


            $sum -= $additional->Points;

            if ($sum + $request->Points <= 25 || $adder->Admin == 1 || $adder->Manager == 1) {
                $reciver = User::find($additional->Receiver_Pep);
                $reciver->Temp_Points -= $additional->Points;
                $additional->Note = $request->Note;
                $additional->Points = $request->Points;
                $additional->save();


                $reciver->Temp_Points += $request->Points;
                $reciver->save();
                return $this->returnSuccessMessage("done");
            } else {
                return $this->returnError('E200', 'لا يمكنك إضافة أكثر من 25 نقطة في اليوم الواحد');
            }

        } else {
            return $this->returnError('E200', 'هاه ؟ ');
        }
    }

    public function view_additional_points(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Receiver_Pep',
                'Sender_Per',
                'Created_At'
            );


            $additional = Additional_Poit::query();
            if ($request->Sender_Per != null) {
                $additional->where('Sender_Per', $request->Sender_Per['ID_Person']);

            }
            if ($request->Receiver_Pep != null) {
                $additional->where('Receiver_Pep', $request->Receiver_Pep['ID_Person']);

            }
            if ($request->Created_At != null) {
                $additional->where('created_at', date("Y-m-d", strtotime($request->Created_At)));

            }
            $res = $additional->get();
            if (count($res) != 0) {
                
                foreach ($res as $i) {
                    $sender = User::where("ID_Person", $i->Sender_Per)->select('ID_Person', 'First_Name', 'Last_Name')->first();
                    $i->Sender_Per = $sender;
                    $created_at = $i->created_at;
                    unset($i->created_at);
                    $i->Created_At = date("Y-m-d", strtotime($created_at));
                }
                foreach ($res as $i) {
                    $reciver = User::where("ID_Person", $i->Receiver_Pep)->select('ID_Person', 'First_Name', 'Last_Name')->first();
                    $i->Receiver_Pep = $reciver;

                }
                return $this->returnData('additional_points', $res);

            } else if ($request->Sender_Per['ID_Person'] == null && $request->Receiver_Pep['ID_Person'] == null && $request->Created_At == null) {
                $res = Additional_Poit::all();
                foreach ($res as $i) {
                    $sender = User::where("ID_Person", $i->Sender_Per)->select('ID_Person', 'First_Name', 'Last_Name')->first();
                    $i->Sender_Per = $sender;
                    $created_at = $i->created_at;
                    unset($i->created_at);
                    $i->Created_At = date("Y-m-d", strtotime($created_at));
                }
                foreach ($res as $i) {
                    $reciver = User::where("ID_Person", $i->Receiver_Pep)->select('ID_Person', 'First_Name', 'Last_Name')->first();
                    $i->Receiver_Pep = $reciver;

                }
                return $this->returnData('additional_points', $res);
            } else {
                return $this->returnData('additional_points', []);
            }


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_additional_points(Request $request)
    {
        try {
            $data = $request->only(
                'ID_Additional_Points'
            );

            $validator = Validator::make($data, [
                'ID_Additional_Points' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $additional = Additional_Poit::find($request->ID_Additional_Points);
            $receiver = User::find($additional->Receiver_Pep);
            $user = $request->user();
            $adder = Permission::find($user->ID_Person);
            if ($adder->Admin != 1 && $adder->Manager != 1) {

                $student = Student::find($additional->Receiver_Pep);
                $group = Group::find($student->Group);

                if ($group != null) {
                    if ($group->Moderator != $adder->ID_Person) {

                        return $this->returnError("102", "لا تملك الصلاحيات الكافية");
                    }
                }

            }

            $receiver->Temp_Points -= $additional->Points;
            $receiver->save();
            $additional->delete();
            return $this->returnSuccessMessage('تم الحذف بنجاح');
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    #view add
    #view adds of student 
    public function points()
    {
        $groups = Group::all();
        foreach($groups as $group){
            $points = 0;
            $students = User::wherehas('students', function($q)use($group){$q->where('Group', $group->ID_Group);});
            echo $group->Group_Name;
            foreach($students as $student){
                $points += $student->Temp_Points;
            }
            echo $points;
        }

    }
}