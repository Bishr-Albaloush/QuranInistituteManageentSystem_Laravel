<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Attendance_Evaluation;
use App\Models\Group;
use App\Models\Groups_has_Assistant;
use App\Models\Permission;
use App\Models\Student;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use stdClass;
use Validator;

class AttendanceController extends Controller
{
    //
    use GeneralTrait;
    public function attendance(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'StudentAttendance',
                'attendenceDate',
                'Group'
            );

            $validator = Validator::make($data, [
                'StudentAttendance' => 'required|array',
                'attendenceDate' => 'required',
                'Group' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            $attendancer = $request->user();

            $admin = Permission::find($attendancer->ID_Person);
            if ($admin->Admin != 1 && $admin->Manager != 1) {
               
                    $group = Group::find($request->Group);
                    if ($group != null) {
                        if ($group->Moderator != $attendancer->ID_Person) {
                            return $this->returnError("102", "لا تملك الصلاحيات الكافية");
                        }
                    }
                
            }

            for ($i = 0; $i < count($request->StudentAttendance); $i++) {
                $attendance = [];
                $attendance = Attendance_Evaluation::where('created_at', $request->attendenceDate)->where("Students_Person_ID_Person", $request->StudentAttendance[$i]["Students_Person_ID_Person"])->get();
                if (count($attendance) == 0) {
                    if ($request->StudentAttendance[$i]["Students_Person_ID_Person"] == 6) {

                    }
                    $student = User::find($request->StudentAttendance[$i]["Students_Person_ID_Person"]);
                    if ($request->StudentAttendance[$i]["State_Attendance"] == 6) {

                        $student->Temp_Points += 5;
                        $student->save();
                    }
                    if ($request->StudentAttendance[$i]["State_Garrment"] == 6) {

                        $student->Temp_Points += 5;
                        $student->save();
                    }
                    if ($request->StudentAttendance[$i]["State_Behavior"] == 6) {

                        $student->Temp_Points += 5;
                        $student->save();
                    }

                    $attendance = Attendance_Evaluation::create([

                        'Students_Person_ID_Person' => $request->StudentAttendance[$i]["Students_Person_ID_Person"],

                        'State_Attendance' => $request->StudentAttendance[$i]["State_Attendance"],
                        'State_Garrment' => $request->StudentAttendance[$i]["State_Garrment"],
                        'State_Behavior' => $request->StudentAttendance[$i]["State_Behavior"],
                        'Group' => $request->Group,
                        'created_at' => date($request->attendenceDate)
                    ]);


                } else {
                    $student = User::find($request->StudentAttendance[$i]["Students_Person_ID_Person"]);
                    if ($request->StudentAttendance[$i]["State_Attendance"] < $attendance[0]->State_Attendance) {

                        $student->Temp_Points += 5;
                        $student->save();
                    }
                    if ($request->StudentAttendance[$i]["State_Attendance"] > $attendance[0]->State_Attendance) {

                        $student->Temp_Points -= 5;
                        $student->save();
                    }

                    if ($request->StudentAttendance[$i]["State_Garrment"] < $attendance[0]->State_Garrment) {

                        $student->Temp_Points += 5;
                        $student->save();
                    }
                    if ($request->StudentAttendance[$i]["State_Garrment"] > $attendance[0]->State_Garrment) {

                        $student->Temp_Points -= 5;
                        $student->save();
                    }

                    if ($request->StudentAttendance[$i]["State_Behavior"] < $attendance[0]->State_Behavior) {

                        $student->Temp_Points += 5;
                        $student->save();
                    }
                    if ($request->StudentAttendance[$i]["State_Behavior"] > $attendance[0]->State_Behavior) {

                        $student->Temp_Points -= 5;
                        $student->save();
                    }
                    $attendance[0]->State_Attendance = $request->StudentAttendance[$i]["State_Attendance"];
                    $attendance[0]->State_Garrment = $request->StudentAttendance[$i]["State_Garrment"];
                    $attendance[0]->State_Behavior = $request->StudentAttendance[$i]["State_Behavior"];
                    $attendance[0]->save();

                }
            }
            return $this->returnSuccessMessage('Attendance done successfully');

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function view_attendance(Request $request)
    {

        try {
            $data = $request->only('Group', 'attendenceDate');
            $validator = Validator::make($data, [
                'Group' => 'required',
                'attendenceDate' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $attendancer = $request->user();

            $admin = Permission::find($attendancer->ID_Person);
            if ($admin->Admin != 1 && $admin->Supervisor != 1 && $admin->Manager != 1) {

            
                    $group = Group::find($request->Group);

                    if ($group != null) {
                        if ($group->Moderator != $attendancer->ID_Person) {

                            return $this->returnError("102", "لا تملك الصلاحيات الكافية");
                        }
                    }
                
            }
            $attendance = Attendance_Evaluation::where('created_at', date('Y-m-d', strtotime($request->attendenceDate)))->where("Group", $request->Group)->get();
            $dateswithids = Attendance_Evaluation::where("Group", $request->Group)->select("ID_Attendance", "created_at")->get();
            $dates = [];
            foreach ($dateswithids as $i) {

                array_push($dates, strval(date('Y-m-d', strtotime($i->created_at))));
            }
            $students = [];
            foreach ($attendance as $i) {
                $student = User::where('ID_Person', $i->Students_Person_ID_Person)->select("ID_Person", "First_Name", "Last_Name")->first();

                $i->student = $student;
                unset($i->updated_at);
                unset($i->created_at);
                unset($i->Group);

                array_push($students, $i);

            }
            $dates = array_unique($dates);
            $final_dates = [];
            foreach ($dates as $i) {
                array_push($final_dates, $i);
            }
            $return = new stdClass();
            $return->attendenceDate = $request->attendenceDate;
            $return->Group = $request->Group;
            $return->dates = $final_dates;
            $return->StudentAttendance = $students;

            return $this->returnData('attendance', $return);



        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function view_all_group_attendance(Request $request)
    {
        try {
            $data = $request->only('Group');
            $validator = Validator::make($data, [
                'Group' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $attendance = Attendance_Evaluation::where("Group", $request->Group)->get();

            return $this->returnData('attendance', $attendance);


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function view_student_attendace(Request $request)
    {

        try {
            $data = $request->only('ID_Person');
            $validator = Validator::make($data, [
                'ID_Person' => 'required'
            ]);
            $attendance = Attendance_Evaluation::where("Students_Person_ID_Person", $request->ID_Person)->get();

            return $this->returnData('attendance', $attendance);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


}