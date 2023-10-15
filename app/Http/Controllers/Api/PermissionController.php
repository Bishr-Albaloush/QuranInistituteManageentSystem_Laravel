<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Groups_has_Assistant;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Models\Permission;
use Validator;

class PermissionController extends Controller
{
    //
    use GeneralTrait;
    public function appoint(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'ID_Permission_Pep',
                'Admin',
                'Manager',
                'Supervisor',
                'Moderator',
                'Assisstent',
                'Custom',
                'Reciter',
                'Tester',
                'Seller',
                'Appoint',
                'View_Person',
                'Add_Person',
                'Edit_Person',
                'Delete_Person',
                'View_Group',
                'Add_Group',
                'Delete_Group',
                'Observe',
                'Recite',
                'Test',
                'Sell',
                'Attendance',
                'Evaluation',
                'Level',
                'Note',
                'State',
                'View_Log',
                'Edit_Group',
                'Appoint_Student',
                'supervisor_groups',
                'moderator_groups',
                'assistant_groups',
                'Adder',
                'View_People',
                'View_Attendance',
                'View_Recite',
                'View_Group'
            );

            $validator = Validator::make($data, [
                'ID_Permission_Pep' => 'required',

            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $state = null;
            if ($request->state) {
                $state = $request->state['ID_State'];
            }
            $permission = Permission::create([
                'ID_Permission_Pep' => $request->ID_Permission_Pep,
                'Admin' => $request->Admin,
                'Manager' => $request->Manager,
                'Supervisor' => $request->Supervisor,
                'Moderator' => $request->Moderator,
                'Assisstent' => $request->Assisstent,
                'Custom' => $request->Custom,
                'Reciter' => $request->Reciter,
                'Tester' => $request->Tester,
                'Seller' => $request->Seller,
                'Appoint' => $request->Appoint,
                'View_Person' => $request->View_Person,
                'Add_Person' => $request->Add_Person,
                'Edit_Person' => $request->Edit_Person,
                'Delete_Person' => $request->Delete_Person,
                'View_Group' => $request->View_Group,
                'Add_Group' => $request->Add_Group,
                'Delete_Group' => $request->Delete_Group,
                'Observe' => $request->Observe,
                'Recite' => $request->Recite,
                'Test' => $request->Test,
                'Sell' => $request->Sell,
                'Attendance' => $request->Attendance,
                'Evaluation' => $request->Evaluation,
                'Level' => $request->Level,
                'Note' => $request->Note,
                'State' => $state,
                'View_Log' => $request->View_Log,
                'Edit_Group' => $request->Edit_Group,
                'Appoint_Student' => $request->Appoint_Student,
                'Adder' => $request->Adder,
                'View_People' => $request->View_People,
                'View_Attendance' => $request->View_Attendance,
                'View_Recite' => $request->View_Recite,
                'View_Groups' => $request->View_Groups
            ]);

            if ($request->supervisor_groups) {
                foreach ($request->supervisor_groups as $i) {
                    $group = Group::find($i['ID_Group']);
                    $group->Supervisor = $request->ID_Permission_Pep;
                    $group->save();
                }
            }

            if ($request->moderator_groups) {
                foreach ($request->moderator_groups as $i) {
                    $group = Group::find($i['ID_Group']);
                    $group->Moderator = $request->ID_Permission_Pep;
                    $group->save();

                }
            }
            if ($request->assistant_groups) {
                foreach ($request->assistant_groups as $i) {
                    $group = Groups_has_Assistant::create(['Group' => $i['ID_Group'], 'Assistants' => $request->ID_Permission_Pep]);
                }
            }


            return $this->returnSuccessMessage('person has been appointed');

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_permission(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'ID_Permission_Pep',
                'Admin',
                'Manager',
                'Supervisor',
                'Moderator',
                'Assisstent',
                'Custom',
                'Reciter',
                'Tester',
                'Seller',
                'Appoint',
                'View_Person',
                'Add_Person',
                'Edit_Person',
                'Delete_Person',
                'View_Group',
                'Add_Group',
                'Delete_Group',
                'Observe',
                'Recite',
                'Test',
                'Sell',
                'Attendance',
                'Evaluation',
                'Level',
                'Note',
                'State',
                'View_Log',
                'Edit_Group',
                'Appoint_Student',
                'supervisor_groups',
                'moderator_groups',
                'assitant_groups'

            );

            $validator = Validator::make($data, [
                'ID_Permission_Pep' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $old_supervisor_groups = Group::where("Supervisor", $request->ID_Permission_Pep)->get();
            $old_assistant_groups = Groups_has_Assistant::where('Assistants', $request->ID_Permission_Pep)->get();
            $old_moderator_groups = Group::where("Moderator", $request->ID_Permission_Pep)->get();

            $state = null;
            if ($request->state) {
                $state = $request->state['ID_State'];
            }

            $permission = Permission::find($request->ID_Permission_Pep);

            $permission->Admin = $request->Admin;
            $permission->Manager = $request->Manager;
            $permission->Supervisor = $request->Supervisor;
            $permission->Moderator = $request->Moderator;
            $permission->Assisstent = $request->Assisstent;
            $permission->Custom = $request->Custom;
            $permission->Reciter = $request->Reciter;
            $permission->Tester = $request->Tester;
            $permission->Seller = $request->Seller;
            $permission->Appoint = $request->Appoint;
            $permission->View_Person = $request->View_Person;
            $permission->Add_Person = $request->Add_Person;
            $permission->Edit_Person = $request->Edit_Person;
            $permission->Delete_Person = $request->Delete_Person;
            $permission->View_Group = $request->View_Group;
            $permission->Add_Group = $request->Add_Group;
            $permission->Delete_Group = $request->Delete_Group;
            $permission->Observe = $request->Observe;
            $permission->Recite = $request->Recite;
            $permission->Test = $request->Test;
            $permission->Sell = $request->Sell;
            $permission->Attendance = $request->Attendance;
            $permission->Evaluation = $request->Evaluation;
            $permission->Level = $request->Level;
            $permission->Note = $request->Note;
            $permission->State = $state;
            $permission->View_Log = $request->View_Log;
            $permission->Edit_Group = $request->Edit_Group;
            $permission->Appoint_Student = $request->Appoint_Student;
            $permission->Adder = $request->Adder;
            $permission->View_People = $request->View_People;
            $permission->View_Attendance = $request->View_Attendance;
            $permission->View_Recite = $request->View_Recite;
            $permission->View_Groups = $request->View_Groups;
            $permission->save();

            foreach ($old_supervisor_groups as $i) {
                $i['Supervisor'] = null;
                $i->save();
            }

            if ($request->supervisor_groups) {
                foreach ($request->supervisor_groups as $i) {
                    $group = Group::find($i['ID_Group']);
                    $group->Supervisor = $request->ID_Permission_Pep;
                    $group->save();
                }
            }
            foreach ($old_moderator_groups as $i) {
                $i['Moderator'] = null;
                $i->save();
            }
            if ($request->moderator_groups) {

                foreach ($request->moderator_groups as $i) {
                    $group = Group::find($i['ID_Group']);
                    $group->Moderator = $request->ID_Permission_Pep;
                    $group->save();

                }
            }
            foreach ($old_assistant_groups as $i) {
                $group = Groups_has_Assistant::where('Group', $i['Group'])->where('Assistants', $i['Assistants'])->delete();


            }
            if ($request->assistant_groups) {
                foreach ($request->assistant_groups as $i) {
                    $group = Groups_has_Assistant::create(['Group' => $i['ID_Group'], 'Assistants' => $request->ID_Permission_Pep]);
                }
            }


            return $this->returnSuccessMessage('permission has been edited');


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }


    public function delete_permission(Request $request)
    {
        try {
            $permission = Permission::find($request->ID_Permission_Pep);
            $result = $permission->delete();
            return $this->returnSuccessMessage("permission has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}