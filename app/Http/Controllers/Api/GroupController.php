<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance_Evaluation;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Groups_has_Assistant;
use App\Models\Student;

class GroupController extends Controller
{
    //
    use GeneralTrait;
    public function create_group(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Group_Name',
                'Private_Meeting',
                'Supervisor',
                'Moderator',
                'students',
                'Assistants',
                'Class'
            );

            $validator = Validator::make($data, [
                'Group_Name' => 'required|string',
                'students' => 'array',
                'Assistants' => 'array',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $moderator = null;
            if ($request->Moderator) {
                $moderator = $request->Moderator['ID_Permission_Pep'];
            }

            $supervisor = null;
            if ($request->Supervisor) {
                $supervisor = $request->Supervisor['ID_Permission_Pep'];
            }

            $group = Group::create([
                'Group_Name' => $request->Group_Name,
                'Private_Meeting' => $request->Private_Meeting,
                'Supervisor' => $supervisor,
                'Moderator' => $moderator,
                'Class' => $request->Class,
                'State' => 2
            ]);

            foreach ($request->students as $studentid) {
                $student = Student::find($studentid['ID_Student_Pep']);
                $student->Group = $group->ID_Group;
                $student->save();
            }

            foreach ($request->Assistants as $assistantid) {
                $relation = Groups_has_Assistant::create([
                    'Group' => $group->ID_Group,
                    'Assistants' => $assistantid['ID_Permission_Pep'],
                ]);
            }

            return $this->returnSuccessMessage('group has been created');

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_group(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'ID_Group',
                'Group_Name',
                'Private_Meeting',
                'Supervisor',
                'Moderator',
                'students',
                'Assistants',
                'Class',
                'State'

            );

            $validator = Validator::make($data, [
                'ID_Group',
                'Group_Name' => 'required|string',
                'Students' => 'array',
                'Assistants' => 'array',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $moderator = null;
            if ($request->Moderator) {
                $moderator = $request->Moderator['ID_Permission_Pep'];
            }

            $supervisor = null;
            if ($request->Supervisor) {
                $supervisor = $request->Supervisor['ID_Permission_Pep'];
            }
            $state = $request->State;
            if ($request->State == null) {
                $state = 2;
            }

            $group = Group::find($request->ID_Group);
            $group->Group_Name = $request->Group_Name;
            $group->Private_Meeting = $request->Private_Meeting;
            $group->Supervisor = $supervisor;
            $group->Moderator = $moderator;
            $group->Class = $request->Class;
            $group->State = $state;
            $group->save();
            //$old_students = Student::where('Group', $request->ID_Group)->get();

            //foreach($old_students as $i){
            //   $i->Group = null;
            //    $i->save();
            //  }
            if ($request->students) {
                foreach ($request->students as $studentid) {
                    $student = Student::find($studentid['ID_Student_Pep']);
                    $student->Group = $group->ID_Group;
                    $student->save();
                }
            }

            $old_assitants = Groups_has_Assistant::where('Group', $request->ID_Group)->get();
            if ($old_assitants) {
                foreach ($old_assitants as $i) {
                    $group = Groups_has_Assistant::where('Group', $request->ID_Group)->where('Assistants', $i['Assistants'])->delete();


                }
            }
            if ($request->Assistants) {
                foreach ($request->Assistants as $i) {

                    $group = Groups_has_Assistant::create(['Group' => $request->ID_Group, 'Assistants' => $i["ID_Permission_Pep"]]);
                }

            }

            return $this->returnSuccessMessage('group has been modified');

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


    }

    public function view_groups(Request $request)
    {
        try {

            $groups = Group::with([
                'students' => function ($q) {
                    $q->wherehas('state', function ($qq) {
                        $qq->where('State_Name', 'نشط');
                    });
                }
            ])->get();
            foreach ($groups as $group) {


                $group->num_of_students = count($group['students']);
            }
            //$groups = Group::all()->with(['students'=> function ($q) {$q->wherehas('state', function($qq){$qq->where('State_Name', 'نشط');});}]);
            return $this->returnData('groups', $groups);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function view_group(Request $request)
    {
        try {

            $data = $request->only(
                'ID_Group',

            );

            $validator = Validator::make($data, [
                'ID_Group' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $groups = Group::where('ID_Group', $request->ID_Group)->
                with([
                    'students' => function ($q) {
                        $q->with([
                            'state',
                            'user' => function ($qq) {
                                $qq->select("ID_Person", "First_Name", "Last_Name", "Education_ID_Education", "Temp_Points")->with("education");
                            }
                        ])->wherehas('state', function ($qu) {
                            $qu->where('State_Name', 'نشط'); })->select("ID_Student_Pep", "Group", "State");
                    }
                ])->first();
            $groups->Moderator = User::where("ID_Person", $groups->Moderator)->select("ID_Person", "First_Name", "Last_Name")->first();
            $groups->Supervisor = User::where("ID_Person", $groups->Supervisor)->select("ID_Person", "First_Name", "Last_Name")->first();
            $group_assistants = Groups_has_Assistant::where("Group", $request->ID_Group)->get();

            $Assistants = [];
            foreach ($group_assistants as $i) {
                $assistant = User::where("ID_Person", $i->Assistants)->select("ID_Person", "First_Name", "Last_Name")->first();
                array_push($Assistants, $assistant);
            }
            $groups->Assistants = $Assistants;

            //$groups = Group::all()->with(['students'=> function ($q) {$q->wherehas('state', function($qq){$qq->where('State_Name', 'نشط');});}]);
            return $this->returnData('group', $groups);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_group(Request $request)
    {
        try {

            //Validate data
            $data = $request->only(
                'ID_Group',
                'Other_Group',
            );

            $validator = Validator::make($data, [
                'ID_Group' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            $group = Group::find($request->ID_Group);
            $students = Student::where("Group", $request->ID_Group);
            $attendance = Attendance_Evaluation::where("Group", $request->ID_Group);
            if ($request->Other_Group == null) {
                foreach ($students as $i) {
                    $i->Group = null;
                    $i->State = 1;
                    $i->save();
                }
                foreach ($attendance as $i) {
                    $i->Group = null;
                    $i->save();
                }
            } else {
                foreach ($students as $i) {
                    $i->Group = $request->Other_Group;
                    $i->save();
                }
                foreach ($attendance as $i) {
                    $i->Group = $request->Other_Group;
                    $i->save();
                }
            }

            $group->delete();
            return $this->returnSuccessMessage("group has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

}