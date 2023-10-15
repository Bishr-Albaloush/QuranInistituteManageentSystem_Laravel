<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Attendance_Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Traits\GeneralTrait;
use Validator;
class StudentController extends Controller
{
    //
    use GeneralTrait;
    public function appoint_student(Request $request)
    {
       

        try {
            //Validate data
            $data = $request->only(
                'ID_Student_Pep',
                'Register_Date',
                'group',
                'state',
                'Memorizations',
        
            );

            $validator = Validator::make($data, [
                'ID_Student_Pep' => 'required',
                'group' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            
            $state = null;
            if($request->state){
                $state = $request->state['ID_State'];
            }

            $student = Student::create([
                'ID_Student_Pep' => $request->ID_Student_Pep,
                'Register_Date' => $request->Register_Date,
                'Group' => $request->group['ID_Group'],
                'State' => $state,
                'Memorizations' => $request->Memorizations,
            
            ]);

            return $this->returnSuccessMessage('person has been appointed');


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function edit_student(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'ID_Student_Pep',
                'Register_Date',
                'group',
                'state',
                'Memorizations',
                

            );

            $validator = Validator::make($data, [
                'ID_Student_Pep' => 'required',
                'group' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $state = null;
            if($request->state){
                $state = $request->state['ID_State'];
            }
            
            $student = Student::find($request->ID_Student_Pep);
            $student-> Register_Date = $request->Register_Date;
            $student->group = $request->group['ID_Group'];
            $student->State = $state;
            $student->Memorizations = $request->Memorizations;
            $student->save();
            $student_attendance = Attendance_Evaluation::where("Students_Person_ID_Person", $student->ID_Student_Pep);
            foreach($student_attendance as $i){
                $i->Group = $request->group['ID_Group'];
                $i->save();
            }
            return $this->returnSuccessMessage('person has been appointed');

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_student(Request $request)
    {
        try {
            $student = Student::find($request->id);
            $result = $student->delete();
            return $this->returnSuccessMessage("student has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function get_students_for_testers()
    {
        
        try {
            //Validate data
           
            $students = User::wherehas('student', function($q){$q->where('State', 2);})->with(['student'=>function($q){ $q->with(['group']);}])->select('ID_Person', 'First_Name', 'Last_Name')->get();


            return $this->returnData('students', $students);
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }    
    }
}
