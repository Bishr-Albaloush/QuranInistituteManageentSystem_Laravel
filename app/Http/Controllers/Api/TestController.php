<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use App\Traits\GeneralTrait;

class TestController extends Controller
{
    //
    use GeneralTrait;

    public function test(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Tested_Pep',
                'Section',
                'Notes',
                'Mistakes',
                'Notices',
                'Duration',
                'Rate',
                'Tajweed',
                'Questions_Number',
                'Mistakes_Number',
                'Tester_Per',
                'Mark',
                'created_at'
            );

            $validator = Validator::make($data, [
                'Tested_Pep' => 'required',
                'Section' => 'required',
                'Tester_Per' => 'required',
                'Mark' => 'required',
                'Tajweed' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }



            $test = Test::create([
                'Tested_Pep' => $request->Tested_Pep["ID_Person"],
                'Section' => $request->Section,
                'Notes' => $request->Notes,
                'Mistakes' => $request->Mistakes,
                'Notices' => $request->Notices,
                'Duration' => $request->Duration,
                'Tajweed' => $request->Tajweed,
                'Questions_Number' => $request->Questions_Number,
                'Mistakes_Number' => $request->Mistakes_Number,
                'Tester_Per' => $request->Tester_Per["ID_Person"],
                'Mark' => $request->Mark,
                'created_at' => $request->created_at
            ]);
            $user = User::find($request->Tested_Pep["ID_Person"]);
            $user->Temp_Points = $user->Temp_Points + $request->Mark + $request->Tajweed;
            $user->save();


            return $this->returnData('test', $test->where('ID_Test', $test->ID_Test)->select("ID_Test")->first());

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_test(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'ID_Test',
                'Tested_Pep',
                'Section',
                'Notes',
                'Mistakes',
                'Notices',
                'Duration',
                'Rate',
                'Tajweed',
                'Tester_Per',
                'Questions_Number',
                'Mistakes_Number',
                'Mark',
                'created_at'
            );

            $validator = Validator::make($data, [
                'ID_Test' => 'required',
                'Tested_Pep' => 'required',
                'Section' => 'required',
                'Tester_Per' => 'required',
                'Mark'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }


            $recite = Test::find($request->ID_Test);
            $section = $recite->Section;
            $mark = $recite->Mark;
            $tajweed = $recite->Tajweed;
            $recite->Tested_Pep = $request->Tested_Pep['ID_Person'];
            $recite->Section = $request->Section;
            $recite->Questions_Number = $request->Questions_Number;
            $recite->Mistakes_Number = $request->Mistakes_Number;
            $recite->Notes = $request->Notes;
            $recite->Mistakes = $request->Mistakes;
            $recite->Notices = $request->Notices;
            $recite->Duration = $request->Duration;
            $recite->Rate = $request->Rate;
            $recite->Tajweed = $request->Tajweed;
            $recite->Tester_Per = $request->Tester_Per["ID_Person"];
            $recite->Mark = $request->Mark;
            $recite->created_at = $request->created_at;
            $recite->save();
            $user = User::find($request->Tested_Pep["ID_Person"]);
            $user->Temp_Points = $user->Temp_Points + $request->Mark + $request->Tajweed - $mark - $tajweed;
            $user->save();

            $user->Memoraization = str_replace($section, $request->Section, $user->Memoraization);
            $user->save();



            return $this->returnSuccessMessage("test has edited successfully");

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function view_test(Request $request)
    {
        try {
            $data = $request->only(
                'ID_Test'
            );
            $validator = Validator::make($data, [
                'ID_Test' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            $reciting = Test::find($request->ID_Test);
            return $this->returnData('reciting', $reciting);
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());

        }
    }
    public function delete_test(Request $request)
    {
        try {
            $test = Test::find($request->ID_Test);
            $person = User::find($test->Tested_Pep);
            $person->Temp_Points = $person->Temp_Points - $test->Mark - $test->Tajweed;
            $person->save();
            $result = $test->delete();
            return $this->returnSuccessMessage("test has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function tests_in_date_range(Request $request)
    {
        try {
            $person = User::query();
            $res = $person->select("ID_Person", "First_Name", "Last_Name")->with([
                "student" => function ($q) {
                    $q->with("Group");
                },
                "tests" => function ($qq) use ($request) {
                    if ($request->StartDate != null) {
                        $qq->whereDate('created_at', '>=', $request->StartDate);
                    }
                    if ($request->StartDate != null) {
                        $qq->whereDate('created_at', '<=', $request->EndDate);
                    }
                    $qq->select("ID_Test", "Mark", "Tajweed", "Section", "Tested_Pep", "created_at", "Tester_Per", "Mistakes", "Notes")->with([
                        "person_tester" => function ($q) {
                            $q->select("ID_Person", "First_Name", "Last_Name");
                        }
                    ]);
                }
            ])->wherehas("tests", function ($qq) use ($request) {
                    if ($request->StartDate != null) {
                        $qq->whereDate('created_at', '>=', $request->StartDate);
                    }
                    if ($request->StartDate != null) {
                        $qq->whereDate('created_at', '<=', $request->EndDate);
                    }

                })->get();
            


            return $this->returnData("tests", $res);
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function edit_old_test(){
        $test=Test::where("Tester_Per", 1)->get();
        foreach($test as $i){
            $i->created_at = "2020-01-01";
            $i->save();
        }
    }
}