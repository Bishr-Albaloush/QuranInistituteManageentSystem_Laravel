<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Group;
use App\Models\Groups_has_Assistant;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Rate;
use App\Models\Reciting;
use App\Models\Section;
use App\Models\Student;
use App\Models\Test;
use App\Models\User;
use App\Traits\GeneralTrait;
use DB;
use Illuminate\Http\Request;

use Validator;

class RecitingController extends Controller
{
    //
    use GeneralTrait;
    public function recite(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'Reciter_Pep',
                'Page',
                'Notes',
                'Mistakes',
                'Notices',
                'Duration',
                'Rates_ID_Rate',
                'Listner_Per',
                'created_at',
                'Tajweed'
            );

            $validator = Validator::make($data, [
                'Reciter_Pep' => 'required',
                'Page' => 'required',
                'Listner_Per' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            $listner = $request->user();

            $student = Student::find($request->Reciter_Pep['ID_Person']);
            $admin = Permission::find($listner->ID_Person);
            if ($admin->Admin != 1 && $admin->Manager != 1) {
                $assistants = Groups_has_Assistant::where('Assistants', $listner->ID_Person)->where('Group', $student->Group)->first();
                if ($assistants == null) {
                    $group = Group::find($student->Group);
                    if ($group->Moderator != $listner->ID_Person) {

                        return $this->returnError("102", "this is not ur student");
                    }
                }
            }

            $memoz = Reciting::where('Reciter_Pep', $request->Reciter_Pep['ID_Person'])->get();
            $page_of_recite = Page::find($request->Page);

            $old_recited_pages = Page::where('Sections_ID_Section', $page_of_recite->Sections_ID_Section)->wherehas('recitings', function ($q) use ($request) {
                $q->where('Reciter_Pep', $request->Reciter_Pep);
            })->get();
            if (count($old_recited_pages) == 0) {
                foreach ($memoz as $i) {
                    $page = Page::find($i->Page);
                    $test = [];
                    $test = Test::where('Section', $page->Sections_ID_Section)->where('Tested_Pep', $request->Reciter_Pep['ID_Person'])->get();
                    if (count($test) == 0) {
                        if ($page->Sections_ID_Section != $page_of_recite->Sections_ID_Section) {
                            return $this->returnError('E900', "هذا الطالب قد بدأ بتسميع جزء آخر ولم يسبره");
                        }

                    }

                }
            }

            $recite = Reciting::create([
                'Reciter_Pep' => $request->Reciter_Pep['ID_Person'],
                'Page' => $request->Page,
                'Notes' => $request->Notes,
                'Mistakes' => $request->Mistakes,
                'Notices' => $request->Notices,
                'Duration' => $request->Duration,
                'Rates_ID_Rate' => $request->Rates_ID_Rate,
                'Record_URL' => $request->Record_URL,
                'Listner_Per' => $request->Listner_Per["ID_Person"],
                'created_at' => $request->created_at
            ]);
            $idrecite = $recite->ID_Recting;
            if ($request->Listner_Per["ID_Person"] != 1) {

                $user = User::find($request->Reciter_Pep["ID_Person"]);

                $rate = Rate::find($request->Rates_ID_Rate);
                $user->Temp_Points = $user->Temp_Points + $rate->Point_Count;

                $user->save();

            }


            return $this->returnData("reciting_id", $idrecite);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_reciting(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'ID_Recting',
                'Reciter_Pep',
                'Page',
                'Notes',
                'Mistakes',
                'Notices',
                'Duration',
                'Rates_ID_Rate',
                'Listner_Per',
                'created_at',
                'Tajweed'
            );

            $validator = Validator::make($data, [
                'ID_Recting' => 'required',
                'Reciter_Pep' => 'required',
                'Page' => 'required',
                'Listner_Per' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $listner = $request->user();

            $student = Student::find($request->Reciter_Pep['ID_Person']);
            $admin = Permission::find($listner->ID_Person);
            if ($admin->Admin != 1 && $admin->Manager != 1) {

                $group = Group::find($student->Group);
                if ($group->Moderator != $listner->ID_Person) {
                    return $this->returnError("102", "this is not ur student");
                }
            }

            $recite = Reciting::find($request->ID_Recting);
            $old_rate = Rate::find($recite->Rates_ID_Rate);
            $old_Listner = $recite->Listner_Per;
            $recite->Reciter_Pep = $request->Reciter_Pep["ID_Person"];
            $recite->Page = $request->Page;
            $recite->Notes = $request->Notes;
            $recite->Mistakes = $request->Mistakes;
            $recite->Notices = $request->Notices;
            $recite->Duration = $request->Duration;
            $recite->Rates_ID_Rate = $request->Rates_ID_Rate;
            $recite->Listner_Per = $request->Listner_Per["ID_Person"];
            $recite->created_at = $request->created_at;
            $recite->save();

            $user = User::find($request->Reciter_Pep["ID_Person"]);
            $rate = Rate::find($request->Rates_ID_Rate);
            if ($request->Listner_Per["ID_Person"] != 1 && $old_Listner != 1) {
                $user->Temp_Points = $user->Temp_Points + $rate->Point_Count - $old_rate->Point_Count;
            } else if ($old_Listner != 1) {
                $user->Temp_Points = $user->Temp_Points - $old_rate->Point_Count;
            } else if ($request->Listner_Per["ID_Person"] != 1) {
                $user->Temp_Points = $user->Temp_Points + $rate->Point_Count;
            }
            $user->save();

            return $this->returnSuccessMessage("reciting has edited successfully");


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_reciting(Request $request)
    {
        try {
            $reciting = Reciting::find($request->ID_Reciting);
            $rate = Rate::find($reciting->Rates_ID_Rate);
            $person = User::find($reciting->Reciter_Pep);
            $person->Temp_Points -= $rate->Point_Count;
            $person->save();
            $result = $reciting->delete();
            return $this->returnSuccessMessage("Reciting has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function view_recite(Request $request)
    {
        try {
            $data = $request->only(
                'ID_Reciting'
            );
            $validator = Validator::make($data, [
                'ID_Reciting' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            $reciting = Reciting::find($request->id);
            return $this->returnData('reciting', $reciting);
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());

        }
    }
    public function create_pages()
    {

        $user = Student::where("State", 1)->get();
        foreach ($user as $i) {
            $i->State = null;
            $i->save();
        }

    }

    public function push_recites(Request $request)
    {
        set_time_limit(0);
        // $data = $request->only("data");
        // foreach($data as $i){
        //   foreach($i as $j){
        //  foreach($j as $k){
        //    foreach($k as $l){
        //    echo $l;
        //  echo "\n";
        //  }
        //     }
        //   }
        // }

        $i = $request->only("data");


        $students = $request['students'];
        $delete_recites = Reciting::where("Rates_ID_Rate", null)->get();
        foreach ($delete_recites as $any) {
            $any->delete();
        }
        foreach ($students as $j) {
            $person_id = $j[0]["person_id"];
            $user = User::where("ID_Person", $person_id)->select("ID_Person", "First_Name", "Last_Name")->first();
            echo $user->First_Name;
            echo " ";
            echo $user->Last_Name;
            echo "\n";
            $id = $user->ID_Person;
            $student = Student::where("ID_Student_Pep", $user->ID_Person)->first();

            $jz2 = 0;

            for ($k = 0; $k < count($j); $k++) {

                if ($k == 2 || $k == 3 || $k == 1 || $k == 0 || $k == 4) {
                    continue;
                }
                if (is_array($j)) {

                    if (is_array($j[$k])) {
                        echo 2;
                        if (array_key_exists("الاسم", $j[$k])) {
                            if ($j[$k]["الاسم"] == "الســـــــــــــــبـــر") {
                                echo $jz2;
                                $jz2++;
                            }
                        }
                        if (array_key_exists("الاسم", $j[$k])) {
                            if (($j[$k]["الاسم"] == "m") || ($j[$k]["الاسم"] == "j") || ($j[$k]["الاسم"] == "jd")) {
                                $j[$k]["الاسم"] = null;

                            }
                        }
                        foreach (array_values($j[$k]) as $vl) {
                            echo $vl;
                        }
                        echo "\n";
                        if (in_array("m", array_values($j[$k]), true) || in_array("j", array_values($j[$k]), true) || in_array("jd", array_values($j[$k]), true)) {


                            if (array_key_exists("Column3", $j[$k])) {


                                if (in_array("m", array_values($j[$k]), true)) {
                                    $rate = 3;
                                } else if (in_array("jd", array_values($j[$k]), true)) {
                                    $rate = 2;
                                } else {
                                    $rate = 1;
                                }
                                echo "\n";
                                $check_recite = Reciting::where("Reciter_Pep", $id)->where("Page", $j[$k]["Column3"])->first();
                                if ($check_recite == null) {
                                    echo "done";
                                    $reciting = Reciting::create([
                                        "Reciter_Pep" => $id,
                                        "Listner_Per" => 1,
                                        "Page" => $j[$k]["Column3"],
                                        "Rates_ID_Rate" => $rate
                                    ]);
                                    if ($j[$k]["Column3"] == 1) {
                                        $reciting = Reciting::create([
                                            "Reciter_Pep" => $id,
                                            "Listner_Per" => 1,
                                            "Page" => 2,
                                            "Rates_ID_Rate" => $rate
                                        ]);
                                    }

                                    if (array_key_exists("Column5", $j[$k])) {
                                        try {
                                            $reciting->created_at = $j[$k]["Column5"];
                                            $reciting->save();
                                        } catch (\Throwable $e) {
                                            echo $e;
                                        }
                                        if (array_key_exists("غير مسلّمة", $j[$k])) {
                                            $reciting->Notes = "الأستاذ الذي سمع له : " . $j[$k]["غير مسلّمة"];
                                            $reciting->save();

                                        }
                                    }
                                }
                            }
                        }
                        if (array_key_exists("الاسم", $j[$k])) {
                            if ($j[$k]["الاسم"] == "الســـــــــــــــبـــر") {
                                if (array_key_exists("غير مسلّمة", $j[$k])) {
                                    $notes = "الأستاذ الذي سبر له : " . $j[$k]["غير مسلّمة"];
                                }
                                $value = null;
                                foreach (array_values($j[$k]) as $q) {
                                    if (gettype($q) == "integer") {
                                        if ($q >= 80 && $q <= 125) {
                                            $value = $q;
                                            break;
                                        }
                                    }
                                }
                                if ($value != null) {
                                    $check_test = Test::where("Section", $jz2)->where("Tested_Pep", $id)->first();
                                    if ($check_test == null) {
                                        $test = Test::create([
                                            "Tester_Per" => 1,
                                            "Tested_Pep" => $id,
                                            "Section" => $jz2,
                                            "Notes" => $notes,
                                            "Mark" => $value
                                        ]);
                                    }

                                }
                            }
                        }
                    }
                }
                $user = null;
            }
        }
    }

    public function view_memorization(Request $request)
    {
        try {
            $data = $request->only('ID_Student_Pep');
            $memorizations = Section::with([
                'test' => function ($q) use ($request) {
                    $q->where("Tested_Pep", $request->ID_Student_Pep)->select(
                        'ID_Test',
                        "Section",
                        "Notes",
                        "Mistakes",
                        "Tajweed",
                        "Tester_Per",
                        "Tested_Pep",
                        "Mark",
                        "created_at",
                        'Tajweed'
                    );
                },
                'pages' => function ($qq) use ($request) {

                    $qq->with([
                        'recitings' => function ($qqq) use ($request) {
                            $qqq->where("Reciter_Pep", $request->ID_Student_Pep)->select(
                                'ID_Recting',
                                "Page",
                                "Notes",
                                "Mistakes",
                                "Duration",
                                "Listner_Per",
                                "Rates_ID_Rate",
                                "created_at",
                                "Reciter_Pep",
                                'Tajweed'
                            );

                        }
                    ])->select("ID_Page", "Sections_ID_Section", "Surah");
                }
            ])->get();
            foreach ($memorizations as $i) {
                $test = $i->test->first();
                unset($i->test);
                $i->test = $test;
                if ($i->test != null) {

                    $i->test->Section = (int) $i->test->Section;
                    $tested = User::where("ID_Person", $i->test->Tested_Pep)->select("ID_Person", "First_Name", "Last_Name")->first();
                    $i->test->Tested_Pep = $tested;

                    $tester = User::where("ID_Person", $i->test->Tester_Per)->select("ID_Person", "First_Name", "Last_Name")->first();
                    $i->test->Tester_Per = $tester;

                }
                foreach ($i->pages as $j) {
                    $j->reciting = $j->recitings->first();
                    if ($j->reciting != null) {
                        $reciter = User::where("ID_Person", $j->reciting->Reciter_Pep)->select("ID_Person", "First_Name", "Last_Name")->first();
                        $j->reciting->Reciter_Pep = $reciter;

                        $listner = User::where("ID_Person", $j->reciting->Listner_Per)->select("ID_Person", "First_Name", "Last_Name")->first();
                        $j->reciting->Listner_Per = $listner;

                        $j->reciting->Page = (int) $j->reciting->Page;
                        $j->reciting->Rates_ID_Rate = (int) $j->reciting->Rates_ID_Rate;
                        $j->reciting->Duration = (int) $j->reciting->Duration;


                    }

                    unset($j->recitings);
                }
            }

            return $this->returnData('memorizations', $memorizations);


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

}