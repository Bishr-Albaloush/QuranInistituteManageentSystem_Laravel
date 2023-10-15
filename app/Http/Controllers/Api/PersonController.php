<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

require_once '../vendor/autoload.php';
require '..\vendor\phpoffice\phpexcel\Classes\PHPExcel.php';
use PHPExcel;
use PHPExcel_IOFactory;
use App\Models\Test;
use App\Models\User;
use App\Models\Phone;
use App\Models\Job;
use App\Models\Education;
use App\Models\Major;
use App\Models\Address;
use App\Models\Mother;
use App\Models\Father;
use App\Models\Kin;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Group;
use App\Models\Student;
use App\Models\Groups_has_Assistant;

use stdClass;
use Validator;
use App\Models\Reciting;
use App\Models\Attendance_Evaluation;
use App\Models\Additional_Poit;
use Illuminate\Support\Facades\DB;


class PersonController extends Controller
{
    //
    use GeneralTrait;
    public function add_person(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'First_Name',
                'Last_Name',
                'Mid_Name',
                'Additional_Name',
                'Birth_Date',
                'call_phone',
                'social_phone',
                'Birth_Place',
                'Email',
                'Image_URL',
                'Distinguishing_Signs',
                'FingerPrinte_Identity',
                'Note',
                'state',
                'job',
                'education',
                'address',
                'mother',
                'father',
                'kin',
                'Points',
                'Memoraization',
                'UserName',
                'Password'
            );
            if ($request->UserName != null) {
                $validator = Validator::make($data, [
                    'UserName' => 'required|string|unique:people',
                ]);
            } else {
                $validator = Validator::make($data, [
                    'First_Name' => 'required',
                    'Last_Name' => 'required'
                ]);
            }

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $job_id = null;
            $major_id = null;
            $education_id = null;
            $address_id = null;
            $mother_id = null;
            $father_id = null;
            $kin_id = null;
            $phone_id = null;
            $phone_id_social = null;
            $check = User::where('First_Name', $request->First_Name)->where('Last_Name', $request->Last_Name)->where('Birth_Date', $request->Birth_Date)->wherehas('father', function ($q) use ($request) {
                $q->where('Father_Name', $request->father['Father_Name']);
            })->wherehas('mother', function ($q) use ($request) {
                $q->where('Mother_Name', $request->mother['Mother_Name']);
            })->wherehas('Kin', function ($q) use ($request) {
                $q->where('Kin_Name', $request->kin['Kin_Name']);
            })->first();
            if ($check) {
                return $this->returnError('E201', 'User already exist');
            }
            if ($request->job) {
                $job = Job::where('Job_Name', $request->job['Job_Name'])->first();
                if (!$job) {
                    $job = Job::create(['Job_Name' => $request->job['Job_Name']]);
                }
                $job_id = $job->ID_Job;
            }


            if ($request->education) {
                if ($request->education['major'] != null) {
                    $major = Major::where('Major_Name', $request->education['major']['Major_Name'])->where('Year', $request->education['major']['Year'])->first();
                    if (!$major) {
                        $major = Major::create(['Major_Name' => $request->education['major']['Major_Name'], 'Year' => $request->education['major']['Year']]);
                    }
                    $major_id = $major->ID_Major;
                }
                $education = Education::where('Education_Type', $request->education['Education_Type'])->where('Major_ID_Major', $major_id)->first();

                if (!$education) {
                    $education = Education::create(['Education_Type' => $request->education['Education_Type'], 'Major_ID_Major' => $major_id]);
                }
                $education_id = $education->ID_Education;

            }

            if ($request->address) {
                $address = Address::where('City', $request->address['City'])->where('Area', $request->address['Area'])->
                    where('Street', $request->address['Street'])->where('Mark', $request->address['Mark'])->first();
                if (!$address) {
                    $address = Address::create([
                        'City' => $request->address['City'],
                        'Area' => $request->address['Area'],
                        'Street' => $request->address['Street'],
                        'Mark' => $request->address['Mark']
                    ]);
                }

                $address_id = $address->ID_Address;
            }

            if ($request->mother) {
                $mother_job_id = null;
                if ($request->mother['job'] != null) {
                    $mother_job = Job::where('Job_Name', $request->mother['job']['Job_Name'])->first();
                    if (!$mother_job) {
                        $mother_job = Job::create(['Job_Name' => $request->mother['job']['Job_Name']]);
                    }
                    $mother_job_id = $mother_job->ID_Job;
                }


                $mother_phone_id = null;
                if ($request->mother['phone'] != null) {
                    $mother_phone = Phone::where('Number', $request->mother['phone']['Number'])->first();
                    if (!$mother_phone) {
                        $mother_phone = Phone::create(['Number' => $request->mother['phone']['Number']]);
                    }
                    $mother_phone_id = $mother_phone->Phone;
                }

                $mother = Mother::where('Mother_Name', $request->mother['Mother_Name'])->where('Phones_Phone', $mother_phone_id)->first();
                if (!$mother) {
                    $mother = Mother::create([
                        'Mother_Name' => $request->mother['Mother_Name'],
                        'State_ID_State' => $request->mother['state']['ID_State'],
                        'Job_ID_Job' => $mother_job_id,
                        'Phones_Phone' => $mother_phone_id
                    ]);
                }
                $mother_id = $mother->ID_Mother;
            }

            if ($request->father != null) {
                $father_job_id = null;
                if ($request->father['job'] != null) {
                    $father_job = Job::where('Job_Name', $request->father['job']['Job_Name'])->first();
                    if (!$father_job) {
                        $father_job = Job::create(['Job_Name' => $request->father['job']['Job_Name']]);
                    }
                    $father_job_id = $father_job->ID_Job;
                }
                $father_phone_id = null;
                if ($request->father['phone'] != null) {
                    $father_phone = Phone::where('Number', $request->father['phone']['Number'])->first();
                    if (!$father_phone) {
                        $father_phone = Phone::create(['Number' => $request->father['phone']['Number']]);
                    }
                    $father_phone_id = $father_phone->Phone;
                }
                $father = Father::where('Father_Name', $request->father['Father_Name'])->where('Phones_Phone', $father_phone_id)->first();
                if (!$father) {
                    $father = Father::create([
                        'Father_Name' => $request->father['Father_Name'],
                        'State_ID_State' => $request->father['state']['ID_State'],
                        'Job_ID_Job' => $father_job_id,
                        'Phones_Phone' => $father_phone_id
                    ]);
                }
                $father_id = $father->ID_Father;


            }

            if ($request->kin) {
                $kin_job_id = null;
                if ($request->kin['job'] != null) {
                    $kin_job = Job::where('Job_Name', $request->kin['job']['Job_Name'])->first();
                    if (!$kin_job) {
                        $kin_job = Job::create(['Job_Name' => $request->kin['job']['Job_Name']]);
                    }
                    $kin_job_id = $kin_job->ID_Job;
                }
                $kin_phone_id = null;
                if ($request->kin['phone']) {
                    $kin_phone = Phone::where('Number', $request->kin['phone']['Number'])->first();
                    if (!$kin_phone) {
                        $kin_phone = Phone::create(['Number' => $request->kin['phone']['Number']]);
                    }
                    $kin_phone_id = $kin_phone->Phone;
                }
                $kin = Kin::where('Kin_Name', $request->kin['Kin_Name'])->where('Phones_Phone', $kin_phone_id)->first();
                if (!$kin) {
                    $kin = Kin::create([
                        'Kin_Name' => $request->kin['Kin_Name'],
                        'State_ID_State' => $request->kin['state']['ID_State'],
                        'Job_ID_Job' => $kin_job_id,
                        'Phones_Phone' => $kin_phone_id
                    ]);
                }
                $kin_id = $kin->ID_Kin;

            }

            if ($request->hasFile('Image_URL')) {
                $filename = $request->file('Image_URl')->store('posts', 'public');
            } else {
                $filename = "posts/DEFAULT.jpg";
            }

            if ($request->call_phone) {
                $phone_number = Phone::where('Number', $request->call_phone['Number'])->first();
                if (!$phone_number) {
                    $phone_number = Phone::create(['Number' => $request->call_phone['Number']]);
                }
                $phone_id = $phone_number->Phone;
            }

            if ($request->social_phone) {
                $social_phone = Phone::where('Number', $request->social_phone['Number'])->first();
                if (!$social_phone) {
                    $social_phone = Phone::create(['Number' => $request->social_phone['Number']]);
                }
                $phone_id_social = $social_phone->Phone;
            }

            $state = null;
            if ($request->state) {
                $state = $request->state['ID_State'];
            }
            $password = bcrypt(0000);
            if ($request->Password) {
                $password = bcrypt($request->Password);
            }
            $creater = $request->user();
            $user = User::create([
                'First_Name' => $request->First_Name,
                'Last_Name' => $request->Last_Name,
                'Mid_Name' => $request->Mid_Name,
                'Additional_Name' => $request->Additional_Name,
                'Birth_Date' => $request->Birth_Date,
                'Birth_Place' => $request->Birth_Place,
                'Email' => $request->Email,
                'Image_URL' => $filename,
                'Distinguishing_Signs' => $request->Distinguishing_Signs,
                'Note' => $request->Note,
                'State_ID_State' => $state,
                'Job_ID_Job' => $job_id,
                'Phone_Number_Call' => $phone_id,
                'Phone_Number_Social' => $phone_id_social,
                'Education_ID_Education' => $education_id,
                'Address_ID_Address' => $address_id,
                'Mothers_ID_Mother' => $mother_id,
                'Kins_ID_Kin' => $kin_id,
                'Memoraization' => $request->Memoraization,
                'Points' => $request->Points,
                'Fathers_ID_Father' => $father_id,
                'UserName' => $request->UserName,
                'Password' => $password,
                'created_by' => $creater->ID_Person
            ]);
            if ($request->UserName == null) {
                $username = $request->First_Name . ' ' . $request->Last_Name;
                $user->UserName = $user->ID_Person . '_' . $username;
                $user->save();
            }

            return $this->returnData('personID', $user->ID_Person);

        } catch (\Error $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function ViewAllPeople(Request $request)
    {
        try {
            $people = User::select("ID_Person", "First_Name", "Last_Name")->get();
            $state = null;
            if ($request->State != null) {
                if ($request->State == 1) {

                }
            }
            return $this->returnData('people', $people);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function add_image(Request $request)
    {
        try {
            $data = $request->only(
                'ID_Person',
                'Image_URL'
            );
            $validator = Validator::make($data, [
                'ID_Person' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }
            if ($request->hasFile('Image_URL')) {
                $filename = $request->file('Image_URL')->store('public/images');
            } else {
                $filename = "posts/DEFAULT.jpg";
            }

            $user = User::find($request->ID_Person);
            $user->Image_URL = $filename;
            return $this->returnData('heee', "niceeee");

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function update_person(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'ID_Person',
                'First_Name',
                'Last_Name',
                'Mid_Name',
                'Additional_Name',
                'Birth_Date',
                'call_phone',
                'social_phone',
                'Birth_Place',
                'Email',
                'Image_URL',
                'Distinguishing_Signs',
                'FingerPrinte_Identity',
                'Note',
                'state',
                'job',
                'education',
                'address',
                'mother',
                'father',
                'kin',
                'Points',
                'Memoraization',
                'Password',
                'UserName'
            );

            $validator = Validator::make($data, [
                'ID_Person' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }

            $job_id = null;
            $major_id = null;
            $education_id = null;
            $address_id = null;
            $mother_id = null;
            $father_id = null;
            $kin_id = null;
            $phone_id = null;
            $phone_id_social = null;

            $user = User::where('ID_Person', $request->ID_Person)->with(['permission'])->first();
            $isAdmin = Permission::where('ID_Permission_Pep', $user->ID_Person)->where('Admin', '1')->first();

            $editor = $request->user();
            $admin = Permission::where('ID_Permission_Pep', $editor->ID_Person)->first();


            if ($isAdmin) {
                if ($admin) {
                    if ($admin->Admin != 1) {
                        return $this->returnError('004', "العاب غيرها");
                    }
                }
            } {
                if ($editor->ID_Person != $user->ID_Person) {
                    if ($admin != null) {
                        if ($admin->Admin != 1 && $admin->Manager != 1 && $admin->Adder != 1) {

                            return $this->returnError('004', "لا تملك صلاحيات لتعديل هذا الحساب");
                        }
                    }
                }
            }

            if ($request->job) {
                $job = Job::where('Job_Name', $request->job['Job_Name'])->first();
                if (!$job) {
                    $job = Job::create(['Job_Name' => $request->job['Job_Name']]);
                }
                $job_id = $job->ID_Job;
            }


            if ($request->education) {
                if ($request->education['major'] != null) {
                    $major = Major::where('Major_Name', $request->education['major']['Major_Name'])->where('Year', $request->education['major']['Year'])->first();
                    if (!$major) {
                        $major = Major::create(['Major_Name' => $request->education['major']['Major_Name'], 'Year' => $request->education['major']['Year']]);
                    }
                    $major_id = $major->ID_Major;
                }
                $education = Education::where('Education_Type', $request->education['Education_Type'])->where('Major_ID_Major', $major_id)->first();

                if (!$education) {
                    $education = Education::create(['Education_Type' => $request->education['Education_Type'], 'Major_ID_Major' => $major_id]);
                }
                $education_id = $education->ID_Education;

            }

            if ($request->address) {
                $address = Address::where('City', $request->address['City'])->where('Area', $request->address['Area'])->
                    where('Street', $request->address['Street'])->where('Mark', $request->address['Mark'])->first();
                if (!$address) {
                    $address = Address::create([
                        'City' => $request->address['City'],
                        'Area' => $request->address['Area'],
                        'Street' => $request->address['Street'],
                        'Mark' => $request->address['Mark']
                    ]);
                }

                $address_id = $address->ID_Address;
            }

            if ($request->mother != null) {
                $mother_job_id = null;
                if ($request->mother['job'] != null) {
                    $mother_job = Job::where('Job_Name', $request->mother['job']['Job_Name'])->first();
                    if (!$mother_job) {
                        $mother_job = Job::create(['Job_Name' => $request->mother['job']['Job_Name']]);
                    }
                    $mother_job_id = $mother_job->ID_Job;
                }


                $mother_phone_id = null;
                if ($request->mother['phone'] != null) {
                    $mother_phone = Phone::where('Number', $request->mother['phone']['Number'])->first();
                    if (!$mother_phone) {
                        $mother_phone = Phone::create(['Number' => $request->mother['phone']['Number']]);
                    }
                    $mother_phone_id = $mother_phone->Phone;
                }

                if ($request->mother['ID_Mother'] != null) {
                    $mother = Mother::find($request->mother['ID_Mother']);
                    $mother->Mother_Name = $request->mother['Mother_Name'];
                    $mother->State_ID_State = $request->mother['state']['ID_State'];
                    $mother->Job_ID_Job = $mother_job_id;
                    $mother->Phones_Phone = $mother_phone_id;
                    $mother->save();
                } else {
                    $mother = Mother::create([
                        'Mother_Name' => $request->mother['Mother_Name'],
                        'State_ID_State' => $request->father['state']['ID_State'],
                        'Job_ID_Job' => $mother_job_id,
                        'Phones_Phone' => $mother_phone_id
                    ]);
                }
                $mother_id = $mother->ID_Mother;
            }

            if ($request->father != null) {
                $father_job_id = null;
                if ($request->father['job'] != null) {
                    $father_job = Job::where('Job_Name', $request->father['job']['Job_Name'])->first();
                    if (!$father_job) {
                        $father_job = Job::create(['Job_Name' => $request->father['job']['Job_Name']]);
                    }
                    $father_job_id = $father_job->ID_Job;
                }
                $father_phone_id = null;
                if ($request->father['phone'] != null) {
                    $father_phone = Phone::where('Number', $request->father['phone']['Number'])->first();
                    if (!$father_phone) {
                        $father_phone = Phone::create(['Number' => $request->father['phone']['Number']]);
                    }
                    $father_phone_id = $father_phone->Phone;
                }

                if ($request->father['ID_Father'] != null) {
                    $father = Father::find($request->father['ID_Father']);
                    $father->Father_Name = $request->father['Father_Name'];
                    $father->State_ID_State = $request->father['state']['ID_State'];
                    $father->Job_ID_Job = $father_job_id;
                    $father->Phones_Phone = $father_phone_id;
                    $father->save();
                } else {
                    $father = Father::create([
                        'Father_Name' => $request->father['Father_Name'],
                        'State_ID_State' => $request->father['state']['ID_State'],
                        'Job_ID_Job' => $father_job_id,
                        'Phones_Phone' => $father_phone_id
                    ]);
                }
                $father_id = $father->ID_Father;


            }

            if ($request->kin) {
                $kin_job_id = null;
                if ($request->kin['job'] != null) {
                    $kin_job = Job::where('Job_Name', $request->kin['job']['Job_Name'])->first();
                    if (!$kin_job) {
                        $kin_job = Job::create(['Job_Name' => $request->kin['job']['Job_Name']]);
                    }
                    $kin_job_id = $kin_job->ID_Job;
                }
                $kin_phone_id = null;
                if ($request->kin['phone']) {
                    $kin_phone = Phone::where('Number', $request->kin['phone']['Number'])->first();
                    if (!$kin_phone) {
                        $kin_phone = Phone::create(['Number' => $request->kin['phone']['Number']]);
                    }
                    $kin_phone_id = $kin_phone->Phone;
                }
                if ($request->kin['ID_Kin'] != null) {
                    $kin = Kin::find($request->kin['ID_Kin']);
                    $kin->Kin_Name = $request->kin['Kin_Name'];
                    $kin->State_ID_State = $request->kin['state']['ID_State'];
                    $kin->Job_ID_Job = $kin_job_id;
                    $kin->Phones_Phone = $kin_phone_id;
                    $kin->save();
                } else {
                    $kin = Kin::create([
                        'Kin_Name' => $request->kin['Kin_Name'],
                        'State_ID_State' => $request->kin['state']['ID_State'],
                        'Job_ID_Job' => $kin_job_id,
                        'Phones_Phone' => $kin_phone_id
                    ]);
                }
                $kin_id = $kin->ID_Kin;

            }

            if ($request->hasFile('Image_URL')) {
                $filename = $request->file('Image_URl')->store('posts', 'public');
            } else {
                $filename = "posts/DEFAULT.jpg";
            }

            if ($request->call_phone) {
                $phone_number = Phone::where('Number', $request->call_phone['Number'])->first();
                if (!$phone_number) {
                    $phone_number = Phone::create(['Number' => $request->call_phone['Number']]);
                }
                $phone_id = $phone_number->Phone;
            }

            if ($request->social_phone) {
                $social_phone = Phone::where('Number', $request->social_phone['Number'])->first();
                if (!$social_phone) {
                    $social_phone = Phone::create(['Number' => $request->social_phone['Number']]);
                }
                $phone_id_social = $social_phone->Phone;
            }

            $state = null;
            if ($request->state) {
                $state = $request->state['ID_State'];
            }

            $user->First_Name = $request->First_Name;
            $user->Last_Name = $request->Last_Name;
            $user->Mid_Name = $request->Mid_Name;
            $user->Additional_Name = $request->Additional_Name;
            $user->Birth_Date = $request->Birth_Date;
            $user->Birth_Place = $request->Birth_Place;
            $user->Email = $request->Email;
            $user->Image_URL = $filename;
            $user->Distinguishing_Signs = $request->Distinguishing_Signs;
            $user->Note = $request->Note;
            $user->State_ID_State = $state;
            $user->Job_ID_Job = $job_id;
            $user->Phone_Number_Call = $phone_id;
            $user->Phone_Number_Social = $phone_id_social;
            $user->Education_ID_Education = $education_id;
            $user->Address_ID_Address = $address_id;
            $user->Mothers_ID_Mother = $mother_id;
            $user->Kins_ID_Kin = $kin_id;
            $user->Fathers_ID_Father = $father_id;
            $user->Memoraization = $request->Memoraization;
            $user->Points = $request->Points;
            $user->UserName = $request->UserName;
            if ($request->Password) {

                $user->Password = bcrypt($request->Password);
            }
            $user->save();

            return $this->returnSuccessMessage('person updated successfully');

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function delete_person(Request $request)
    {
        try {
            $user = User::find($request->ID_Person);
            $permission = Permission::find($request->ID_Person);
            $student = Student::find($request->ID_Person);
            $recittings = Reciting::where("Listner_Per", $request->ID_Person)->get();
            $tests = Test::where("Tester_Per", $request->ID_Person)->get();
            $recitting = Reciting::where("Reciter_Pep", $request->ID_Person)->get();
            $attendance = Attendance_Evaluation::where("Students_Person_ID_Person", $request->ID_Person)->get();
            $test = Test::where("Tested_Pep", $request->ID_Person)->get();
            $groups_assistant = Groups_has_Assistant::where("Assistants", $request->ID_Person)->get();
            $groups_moderator = Group::where("Moderator", $request->ID_Person)->get();
            $groups_supervisor = Group::where("Supervisor", $request->ID_Person)->get();
            
            if ($permission != null) {
                foreach ($groups_moderator as $i) {
                    $i->Moderator = null;
                    $i->save();
                }
                foreach ($groups_supervisor as $i) {
                    $i->Supervisor = null;
                    $i->save();
                }
                foreach ($groups_assistant as $i) {
                    $i->delete();
                }
                foreach ($tests as $i) {
                    $i->Tester_Per = 1;
                    $i->save();
                }
                foreach ($recittings as $i) {
                    $i->Listner_Per = 1;
                    $i->save();
                }
                $permission->delete();
            }
           
            if ($student != null) {
                foreach ($recitting as $i) {
                    $i->delete();
                }

                foreach ($test as $i) {
                    $i->delete();
                }

                foreach ($attendance as $i) {
                    $i->delete();
                }
                $student->delete();
            }
            
            $user->delete();
            return $this->returnSuccessMessage("user has been deleted");
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function view_person(Request $request)
    {
        try {
            //Validate data
            $data = $request->only(
                'id'
            );

            $validator = Validator::make($data, [
                'id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->returnError('E200', $validator->messages());
            }



            $user = User::find($request->id);
            $student = Student::find($request->id);
            $attendancer = $request->user();
            $admin = Permission::find($attendancer->ID_Person);
            if ($request->id != $attendancer->ID_Person) {
                if ($admin->Admin != 1 && $admin->Supervisor != 1 && $admin->Manager != 1 && $admin->Adder != 1) {
                    if ($student == null) {
                        return $this->returnError("102", "معلومات هذا الشخص غير متاحة لك");
                    }

                    $group = Group::find($student->Group);

                    if ($group != null) {
                        if ($group->Moderator != $attendancer->ID_Person) {

                            return $this->returnError("102", "معلومات هذا الشخص غير متاحة لك");


                        }
                    }
                }
            }
            $permission = Permission::find($request->id);
            if ($permission) {
                $permission = $permission->with(['supervisor_groups', 'moderator_groups', 'state'])->where("ID_Permission_Pep", $user->ID_Person)->first();
                $assistant_groups = Groups_has_Assistant::where('Assistants', $user->ID_Person);

                if ($assistant_groups) {
                    $assistant_groups = $assistant_groups->with(['groups'])->get();
                    $real_assistant_groups = [];
                    foreach ($assistant_groups as $i) {
                        array_push($real_assistant_groups, $i->groups);
                    }
                    $permission->assistant_groups = $real_assistant_groups;
                }

            }

            $user = $user->where('ID_Person', $request->id)->with([
                'call_phone',
                'social_phone',
                'mother' => function ($qq) {
                    $qq->with(['phone']);
                    $qq->with(['job']);
                    $qq->with(['state']);

                },
                'father' => function ($qq) {
                    $qq->with(['phone']);
                    $qq->with(['job']);
                    $qq->with(['state']);


                },
                'kin' => function ($qq) {
                    $qq->with(['phone']);
                    $qq->with(['job']);
                    $qq->with(['state']);

                },
                'address',
                'job',
                'education' => function ($qq) {
                    $qq->with(['major']);
                },
                'state',
                'student' => function ($qq) {
                    $qq->with(['group']);
                    $qq->with(['state']);
                },


            ])->first();
            $user->permission = $permission;


            return $this->returnData('person', $user);
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

     public function view_all_people(Request $request)
    {
        try {
            $data = $request->only(
                'First_Name',
                'Last_Name',
                'Mid_Name',
                'Additional_Name',
                'Birth_Date',
                'call_phone',
                'social_phone',
                'Birth_Place',
                'Email',
                'Image_URL',
                'Distinguishing_Signs',
                'FingerPrinte_Identity',
                'Note',
                'state',
                'job',
                'education',
                'address',
                'mother',
                'father',
                'kin',
                'Points',
                'Memoraization',
                'UserName',

            );
            $people = User::
                when($request->First_Name != null && $request->First_Name != '', function ($people) use ($request) {
                    $people->when($request->First_Name != -1, function ($people) use ($request) {
                        $people->where('First_Name', 'like', '%' . $request->First_Name . '%');
                    })->
                        when($request->First_Name == -1, function ($people) use ($request) {
                            $people->where('First_Name', null);
                        });
                })->
                when($request->Last_Name != null && $request->Last_Name != '', function ($people) use ($request) {
                    $people->when($request->Last_Name != -1, function ($people) use ($request) {
                        $people->where('Last_Name', 'like', '%' . $request->Last_Name . '%');
                    })->
                        when($request->Last_Name == -1, function ($people) use ($request) {
                            $people->where('Last_Name', null);
                        });
                })->
                when($request->Mid_Name != null && $request->Mid_Name != '', function ($people) use ($request) {
                    $people->when($request->Mid_Name != -1, function ($people) use ($request) {
                        $people->where('Mid_Name', 'like', '%' . $request->Mid_Name . '%');
                    })->
                        when($request->Mid_Name == -1, function ($people) use ($request) {
                            $people->where('Mid_Name', null);
                        });
                })->
                when($request->Birth_Date != null && $request->Birth_Date != '', function ($people) use ($request) {
                    $people->when($request->Birth_Date != -1, function ($people) use ($request) {
                        $people->where('Birth_Date', 'like', '%' . $request->Birth_Date . '%');
                    })->
                        when($request->Birth_Date == -1, function ($people) use ($request) {
                            $people->where('Birth_Date', null);
                        });
                })->
                when($request->Birth_Place != null && $request->Birth_Place != '', function ($people) use ($request) {
                    $people->when($request->Birth_Place != -1, function ($people) use ($request) {
                        $people->where('Birth_Place', 'like', '%' . $request->Birth_Place . '%');
                    })->
                        when($request->Birth_Place == -1, function ($people) use ($request) {
                            $people->where('Birth_Place', null);
                        });
                })->
                when($request->Email != null && $request->Email != '', function ($people) use ($request) {
                    $people->when($request->Email != -1, function ($people) use ($request) {
                        $people->where('Email', 'like', '%' . $request->Email . '%');
                    })->
                        when($request->Email == -1, function ($people) use ($request) {
                            $people->where('Email', null);
                        });
                })->
                when($request->Distinguishing_Signs != null && $request->Distinguishing_Signs != '', function ($people) use ($request) {
                    $people->when($request->Distinguishing_Signs != -1, function ($people) use ($request) {
                        $people->where('Distinguishing_Signs', 'like', '%' . $request->Distinguishing_Signs . '%');
                    })->
                        when($request->Distinguishing_Signs == -1, function ($people) use ($request) {
                            $people->where('Distinguishing_Signs', null);
                        });
                })->
                when($request->FingerPrinte_Identity != null && $request->FingerPrinte_Identity != '', function ($people) use ($request) {
                    $people->when($request->FingerPrinte_Identity != -1, function ($people) use ($request) {
                        $people->where('FingerPrinte_Identity', 'like', '%' . $request->FingerPrinte_Identity . '%');
                    })->
                        when($request->FingerPrinte_Identity == -1, function ($people) use ($request) {
                            $people->where('FingerPrinte_Identity', null);
                        });
                })->
                when($request->Note != null && $request->Note != '', function ($people) use ($request) {
                    $people->when($request->Note != -1, function ($people) use ($request) {
                        $people->where('Note', 'like', '%' . $request->Note . '%');
                    })->
                        when($request->Note == -1, function ($people) use ($request) {
                            $people->where('Note', null);
                        });
                })->
                when($request->Points != null && $request->Points != '', function ($people) use ($request) {
                    $people->when($request->Points != -1, function ($people) use ($request) {
                        $people->where('Points', 'like', '%' . $request->Points . '%');
                    })->
                        when($request->Points == -1, function ($people) use ($request) {
                            $people->where('Points', null);
                        });
                })->
                when($request->Memoraization != null && $request->Memoraization != '', function ($people) use ($request) {
                    $people->when($request->Memoraization != -1, function ($people) use ($request) {
                        $people->where('Memoraization', 'like', '%' . $request->Memoraization . '%');
                    })->
                        when($request->Memoraization == -1, function ($people) use ($request) {
                            $people->where('Memoraization', null);
                        });
                })->
                when($request->UserName != null && $request->UserName != '', function ($people) use ($request) {
                    $people->when($request->UserName != -1, function ($people) use ($request) {
                        $people->where('UserName', 'like', '%' . $request->UserName . '%');
                    })->
                        when($request->UserName == -1, function ($people) use ($request) {
                            $people->where('UserName', null);
                        });
                })->
                when($request->job != null && $request->job != '', function ($people) use ($request) {
                    $people->when($request->job['ID_Job'] != -1, function ($people) use ($request) {
                        $people->wherehas('job', function ($q) use ($request) {
                            $q->when($request->job['Job_Name'] != -1, function ($people) use ($request) {
                                $people->where('Job_Name', 'like', '%' . $request->job['Job_Name'] . '%');
                            })->
                                when($request->job['Job_Name'] == -1, function ($people) use ($request) {
                                    $people->where('Job_Name', null);
                                });
                        });
                    })->when($request->job['ID_Job'] == -1, function ($people) use ($request) {
                        $people->where('job', null);
                    });

                })->
                when($request->address != null && $request->address != '', function ($people) use ($request) {
                    $people->wherehas('address', function ($q) use ($request) {
                        $q->when($request->adress['City'] != -1, function ($people) use ($request) {
                            $people->where('City', 'like', '%' . $request->address['City'] . '%');
                        })->
                            when($request->adress['City'] == -1, function ($people) use ($request) {
                                $people->where('City', null);
                            })->
                            when($request->adress['Area'] != -1, function ($people) use ($request) {
                                $people->where('Area', 'like', '%' . $request->address['Area'] . '%');
                            })->
                            when($request->adress['Area'] == -1, function ($people) use ($request) {
                                $people->where('Area', null);
                            })->
                            when($request->adress['Street'] != -1, function ($people) use ($request) {
                                $people->where('Street', 'like', '%' . $request->address['Street'] . '%');
                            })->
                            when($request->adress['Street'] == -1, function ($people) use ($request) {
                                $people->where('Street', null);
                            })->
                            when($request->adress['Mark'] != -1, function ($people) use ($request) {
                                $people->where('Mark', 'like', '%' . $request->address['Mark'] . '%');
                            })->
                            when($request->adress['Mark'] == -1, function ($people) use ($request) {
                                $people->where('Mark', null);
                            });
                    });
                })->
                when($request->state != null && $request->state != '', function ($people) use ($request) {
                    $people->when($request->state['ID_State'] != -1, function ($people) use ($request) {
                        $people->wherehas('state', function ($q) use ($request) {
                            $q->when($request->state['State_Name'] != -1, function ($people) use ($request) {
                                $people->where('State_Name', 'like', '%' . $request->state['State_Name'] . '%');
                            })->
                                when($request->state['State_Name'] == -1, function ($people) use ($request) {
                                    $people->where('State_Name', null);
                                });
                        });
                    })->when($request->state['ID_State'] == -1, function ($people) use ($request) {
                        $people->where('state', null);
                    });
                })->
                when($request->call_phone != null && $request->call_phone != '', function ($people) use ($request) {
                    $people->when($request->call_phone['ID_Phone'] != -1, function ($people) use ($request) {
                        $people->wherehas('call_phone', function ($q) use ($request) {
                            $q->when($request->call_phone['Number'] != -1, function ($people) use ($request) {
                                $people->where('Number', 'like', '%' . $request->call_phone['Number'] . '%');
                            })->
                                when($request->call_phone['Number'] == -1, function ($people) use ($request) {
                                    $people->where('Number', null);
                                });
                        });
                    })->when($request->call_phone['ID_Phone'] == -1, function ($people) use ($request) {
                        $people->where('call_phone', null);
                    });
                })->
                when($request->social_phone != null && $request->social_phone != '', function ($people) use ($request) {
                    $people->when($request->social_phone['ID_Phone'] != -1, function ($people) use ($request) {
                        $people->wherehas('social_phone', function ($q) use ($request) {
                            $q->when($request->social_phone['Number'] != -1, function ($people) use ($request) {
                                $people->where('Number', 'like', '%' . $request->social_phone['Number'] . '%');
                            })->
                                when($request->social_phone['Number'] == -1, function ($people) use ($request) {
                                    $people->where('Number', null);
                                });
                        });
                    })->when($request->social_phone['ID_Phone'] == -1, function ($people) use ($request) {
                        $people->where('social_phone', null);
                    });
                })->
                when($request->education != null && $request->education != '', function ($people) use ($request) {

                    $people->when($request->education['ID_Education'] != -1, function ($people) use ($request) {
                        $people->wherehas('education', function ($q) use ($request) {

                            $q->when($request->education['Education_Type'] != -1, function ($people) use ($request) {
                                $people->where('Education_Type', 'like', '%' . $request->education['Education_Type'] . '%');
                            })->
                                when($request->education['Education_Type'] == -1, function ($people) use ($request) {
                                    $people->where('Education_Type', null);
                                })->
                                when($request->education['major'] != null && $request->education['major'] != '', function ($q) use ($request) {

                                    $q->wherehas('major', function ($qq) use ($request) {

                                        $qq->when($request->education['major']['Major_Name'] != -1, function ($people) use ($request) {
                                            $people->where('Major_Name', 'like', '%' . $request->education['major']['Major_Name'] . '%');
                                        })->
                                            when($request->education['major']['Major_Name'] == -1, function ($people) use ($request) {
                                                $people->where('Major_Name', null);
                                            });
                                    });
                                })->when($request->education['major']['ID_Major'] == -1, function ($people) use ($request) {
                                $people->where('Major_ID_Major', null);
                            });
                        });
                    })->when($request->education['ID_Education'] == -1, function ($people) use ($request) {
                        $people->where('Education_ID_Education', null);
                    });
                })->
                when($request->mother != null && $request->mother != '', function ($people) use ($request) {
                    $people->when($request->mother['ID_Mother'] != -1, function ($people) use ($request) {
                        $people->wherehas('mother', function ($q) use ($request) {
                            $q->when($request->mother['Mother_Name'] != -1, function ($people) use ($request) {
                                $people->where('Mother_Name', 'like', '%' . $request->mother['Mother_Name'] . '%');
                            })->
                                when($request->mother['Mother_Name'] == -1, function ($people) use ($request) {
                                    $people->where('Mother_Name', null);
                                })->
                                when($request->mother['job'] != null && $request->mother['job'] != '', function ($q) use ($request) {
                                    $q->when($request->mother['job'] != -1, function ($people) use ($request) {
                                        $people->wherehas('job', function ($qq) use ($request) {
                                            $qq->when($request->mother['job']['Job_Name'] != -1, function ($people) use ($request) {
                                                $people->where('Job_Name', 'like', '%' . $request->mother['job']['Job_Name'] . '%');
                                            })->
                                                when($request->mother['job']['Job_Name'] == -1, function ($people) use ($request) {
                                                    $people->where('Job_Name', null);
                                                });
                                        });
                                    })->when($request->mother['job'] == -1, function ($people) use ($request) {
                                        $people->where('Job_ID_Job', null);
                                    });
                                })->
                                when($request->mother['state'] != null && $request->mother['state'] != '', function ($q) use ($request) {
                                    $q->when($request->mother['state'] != -1, function ($people) use ($request) {
                                        $people->wherehas('state', function ($qq) use ($request) {
                                            $qq->when($request->mother['state']['State_Name'] != -1, function ($people) use ($request) {
                                                $people->where('State_Name', 'like', '%' . $request->mother['state']['State_Name'] . '%');
                                            })->
                                                when($request->mother['state']['State_Name'] == -1, function ($people) use ($request) {
                                                    $people->where('State_Name', null);
                                                });
                                        });
                                    })->when($request->mother['state'] == -1, function ($people) use ($request) {
                                        $people->where('State_ID_State', null);
                                    });
                                })->
                                when($request->mother['phone'] != null && $request->mother['phone'] != '', function ($q) use ($request) {
                                    $q->when($request->mother['phone'] != -1, function ($people) use ($request) {
                                        $people->wherehas('phone', function ($qq) use ($request) {
                                            $qq->when($request->mother['phone']['Number'] != -1, function ($people) use ($request) {
                                                $people->where('Number', 'like', '%' . $request->mother['phone']['Number'] . '%');
                                            })->
                                                when($request->mother['phone']['Number'] == -1, function ($people) use ($request) {
                                                    $people->where('Number', null);
                                                });
                                        });
                                    })->when($request->mother['phone'] == -1, function ($people) use ($request) {
                                        $people->where('Phones_Phone', null);
                                    });
                                });
                        });
                    });
                })->
                when($request->father != null && $request->father != '', function ($people) use ($request) {
                    $people->when($request->father['ID_Father'] != -1, function ($people) use ($request) {
                        $people->wherehas('father', function ($q) use ($request) {
                            $q->when($request->father['Father_Name'] != -1, function ($people) use ($request) {
                                $people->where('Father_Name', 'like', '%' . $request->father['Father_Name'] . '%');
                            })->
                                when($request->father['Father_Name'] == -1, function ($people) use ($request) {
                                    $people->where('Father_Name', null);
                                })->
                                when($request->father['job'] != null && $request->father['job'] != '', function ($q) use ($request) {
                                    $q->when($request->father['job'] != -1, function ($people) use ($request) {
                                        $people->wherehas('job', function ($qq) use ($request) {
                                            $qq->when($request->father['job']['Job_Name'] != -1, function ($people) use ($request) {
                                                $people->where('Job_Name', 'like', '%' . $request->father['job']['Job_Name'] . '%');
                                            })->
                                                when($request->father['job']['Job_Name'] == -1, function ($people) use ($request) {
                                                    $people->where('Job_Name', null);
                                                });
                                        });
                                    })->when($request->father['job'] == -1, function ($people) use ($request) {
                                        $people->where('Job_ID_Job', null);
                                    });
                                })->
                                when($request->father['state'] != null && $request->father['state'] != '', function ($q) use ($request) {
                                    $q->when($request->father['state'] != -1, function ($people) use ($request) {
                                        $people->wherehas('state', function ($qq) use ($request) {
                                            $qq->when($request->father['state']['State_Name'] != -1, function ($people) use ($request) {
                                                $people->where('State_Name', 'like', '%' . $request->father['state']['State_Name'] . '%');
                                            })->
                                                when($request->father['state']['State_Name'] == -1, function ($people) use ($request) {
                                                    $people->where('State_Name', null);
                                                });
                                        });
                                    })->when($request->father['state'] == -1, function ($people) use ($request) {
                                        $people->where('State_ID_State', null);
                                    });
                                })->
                                when($request->father['phone'] != null && $request->father['phone'] != '', function ($q) use ($request) {
                                    $q->when($request->father['phone'] != -1, function ($people) use ($request) {
                                        $people->wherehas('phone', function ($qq) use ($request) {
                                            $qq->when($request->father['phone']['Number'] != -1, function ($people) use ($request) {
                                                $people->where('Number', 'like', '%' . $request->father['phone']['Number'] . '%');
                                            })->
                                                when($request->father['phone']['Number'] == -1, function ($people) use ($request) {
                                                    $people->where('Number', null);
                                                });
                                        });
                                    })->when($request->father['phone'] == -1, function ($people) use ($request) {
                                        $people->where('Phones_Phone', null);
                                    });
                                });
                        });
                    });
                })->
                when($request->Additional_Name != null && $request->Additional_Name != '', function ($people) use ($request) {
                    $people->when($request->Additional_Name != -1, function ($people) use ($request) {
                        $people->where('Additional_Name', 'like', '%' . $request->Additional_Name . '%');
                    })->
                        when($request->Additional_Name == -1, function ($people) use ($request) {
                            $people->where('Additional_Name', null);
                        });
                })->
                when($request->kin != null && $request->kin != '', function ($people) use ($request) {
                    $people->when($request->kin['ID_Kin'] != -1, function ($people) use ($request) {
                        $people->wherehas('kin', function ($q) use ($request) {
                            $q->when($request->kin['Kin_Name'] != -1, function ($people) use ($request) {
                                $people->where('Kin_Name', 'like', '%' . $request->kin['Kin_Name'] . '%');
                            })->
                                when($request->kin['Kin_Name'] == -1, function ($people) use ($request) {
                                    $people->where('Kin_Name', null);
                                })->
                                when($request->kin['job'] != null && $request->kin['job'] != '', function ($q) use ($request) {
                                    $q->when($request->kin['job'] != -1, function ($people) use ($request) {
                                        $people->wherehas('job', function ($qq) use ($request) {
                                            $qq->when($request->kin['job']['Job_Name'] != -1, function ($people) use ($request) {
                                                $people->where('Job_Name', 'like', '%' . $request->kin['job']['Job_Name'] . '%');
                                            })->
                                                when($request->kin['job']['Job_Name'] == -1, function ($people) use ($request) {
                                                    $people->where('Job_Name', null);
                                                });
                                        });
                                    })->when($request->kin['job'] == -1, function ($people) use ($request) {
                                        $people->where('Job_ID_Job', null);
                                    });
                                })->
                                when($request->kin['state'] != null && $request->kin['state'] != '', function ($q) use ($request) {
                                    $q->when($request->kin['state'] != -1, function ($people) use ($request) {
                                        $people->wherehas('state', function ($qq) use ($request) {
                                            $qq->when($request->kin['state']['State_Name'] != -1, function ($people) use ($request) {
                                                $people->where('State_Name', 'like', '%' . $request->kin['state']['State_Name'] . '%');
                                            })->
                                                when($request->kin['state']['State_Name'] == -1, function ($people) use ($request) {
                                                    $people->where('State_Name', null);
                                                });
                                        });
                                    })->when($request->kin['state'] == -1, function ($people) use ($request) {
                                        $people->where('State_ID_State', null);
                                    });
                                })->
                                when($request->kin['phone'] != null && $request->kinn['phone'] != '', function ($q) use ($request) {
                                    $q->when($request->kin['phone'] != -1, function ($people) use ($request) {
                                        $people->wherehas('phone', function ($qq) use ($request) {
                                            $qq->when($request->kin['phone']['Number'] != -1, function ($people) use ($request) {
                                                $people->where('Number', 'like', '%' . $request->kin['phone']['Number'] . '%');
                                            })->
                                                when($request->kin['phone']['Number'] == -1, function ($people) use ($request) {
                                                    $people->where('Number', null);
                                                });
                                        });
                                    })->when($request->kin['phone'] == -1, function ($people) use ($request) {
                                        $people->where('Phones_Phone', null);
                                    });
                                });
                        });
                    });
                })->
                when($request->student != null && $request->student != '', function ($people) use ($request) {
                    $people->when($request->student['ID_Student_Pep'] != -1, function ($people) use ($request) {
                        $people->wherehas('student', function ($q) use ($request) {
                            $q->when($request->student['Register_Date'] != null && $request->student['Register_Date'] != '', function ($q) use ($request) {
                                $q->when($request->student['Register_Date'] != -1, function ($people) use ($request) {
                                    $people->where('Register_Date', 'like', '%' . $request->student['Register_Date'] . '%');
                                })->
                                    when($request->student['Register_Date'] == -1, function ($people) use ($request) {
                                        $people->where('Register_Date', null);
                                    });
                            })->
                                when($request->student['group'] != null && $request->student['gruop'] != '', function ($q) use ($request) {
                                    $q->when($request->student['group'] != -1, function ($people) use ($request) {
                                        $people->wherehas('group', function ($qq) use ($request) {
                                            $qq->when($request->student['group']['Group_Name'] != null && $request->student['group']['Group_Name'] != '', function ($q) use ($request) {
                                                $q->when($request->student['group']['Group_Name'] != -1, function ($people) use ($request) {
                                                    $people->where('Group_Name', 'like', '%' . $request->student['group']['Group_Name'] . '%');
                                                })->
                                                    when($request->student['group']['Group_Name'] == -1, function ($people) use ($request) {
                                                        $people->where('Group_Name', null);
                                                    });
                                            })->
                                                when($request->student['group']['Class'] != null && $request->student['group']['Class'] != '', function ($q) use ($request) {
                                                    $q->when($request->student['group']['Class'] != -1, function ($people) use ($request) {
                                                        $people->where('Class', 'like', '%' . $request->student['group']['Class'] . '%');
                                                    })->
                                                        when($request->student['group']['Class'] == -1, function ($people) use ($request) {
                                                            $people->where('Class', null);
                                                        });
                                                })->
                                                when($request->student['group']['Private_Meeting'] != null && $request->student['group']['Private_Meeting'] != '', function ($q) use ($request) {
                                                    $q->when($request->student['group']['Private_Meeting'] != -1, function ($people) use ($request) {
                                                        $people->where('Private_Meeting', 'like', '%' . $request->student['group']['Private_Meeting'] . '%');
                                                    })->
                                                        when($request->student['group']['Private_Meeting'] == -1, function ($people) use ($request) {
                                                            $people->where('Private_Meeting', null);
                                                        });
                                                })->
                                                when($request->student['group']['Create_Date'] != null && $request->student['group']['Create_Date'] != '', function ($q) use ($request) {
                                                    $q->when($request->student['group']['Create_Date'] != -1, function ($people) use ($request) {
                                                        $people->where('Create_Date', 'like', '%' . $request->student['group']['Create_Date'] . '%');
                                                    })->
                                                        when($request->student['group']['Create_Date'] == -1, function ($people) use ($request) {
                                                            $people->where('Create_Date', null);
                                                        });
                                                });
                                        });
                                    });
                                })->
                                when($request->student['state'] != null && $request->student['state'] != '', function ($q) use ($request) {
                                    $q->when($request->student['state'] != -1, function ($people) use ($request) {
                                        $people->wherehas('state', function ($qq) use ($request) {
                                            $qq->when($request->student['state']['State_Name'] != -1, function ($people) use ($request) {
                                                $people->where('State_Name', 'like', '%' . $request->student['state']['State_Name'] . '%');
                                            })->
                                                when($request->student['state']['State_Name'] == -1, function ($people) use ($request) {
                                                    $people->where('State_Name', null);
                                                });
                                        });
                                    })->when($request->student['state'] == -1, function ($people) use ($request) {
                                        $people->where('State_ID_State', null);
                                    });
                                });
                        });
                    });
                })->
                when($request->permission != null && $request->permission != '', function ($people) use ($request) {

                    $people->wherehas('permission');



                })->get();
            return $this->returnData('people', $people);
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    } 

    public function view_all_people_without_religion(Request $request)
    {
        try {
            $people = User::doesntHave('permission')->doesntHave('student')->get();
            return $this->returnData('people', $people);
        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function filter(Request $request)
    {
        try {
            $data = $request->only(
                'First_Name',
                'Last_Name',
                'Mid_Name',
                'Additional_Name',
                'Birth_Date',
                'call_phone',
                'social_phone',
                'Birth_Place',
                'Email',
                'Image_URL',
                'Distinguishing_Signs',
                'FingerPrinte_Identity',
                'Note',
                'state',
                'job',
                'education',
                'address',
                'mother',
                'father',
                'kin',
                'Points',
                'Memoraization',
                'UserName',
                'Password',
                'student'
            );

            $user = User::query();
            if ($request->Student !== null) {
                $student = $request->student;
                $user->whereHas('student')->with(['student']);
                if ($student->state !== null) {
                    $state = $student->state;
                    if ($state->ID_State !== -1) {
                        if ($state->ID_State !== null) {
                            $user->whereHas('student', function ($q) use ($state) {
                                $q->whereHas('state', function ($qq) use ($state) {
                                    $qq->where('ID_State', $state->ID_State);
                                });
                            })->with(['student']);

                        } else {
                            $user->whereHas('student', function ($q) {
                                $q->whereHas('state');
                            })->with(['student']);
                        }
                    } else {
                        $user->whereHas('student', function ($q) {
                            $q->doesntHave('state');
                        })->with(['student']);
                    }
                    $result = $user->get();
                } else {
                    $result = $user->all();
                }

            }


            return $this->returnData('people', $result);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function view_supervisors(Request $request)
    {
        try {
            $user = User::wherehas('permission', function ($q) {
                $q->where('Supervisor', "1");
            })->select('ID_Person', 'First_Name', 'Last_Name')->get();

            return $this->returnData('people', $user);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function view_moderators(Request $request)
    {
        try {
            $user = User::wherehas('permission', function ($q) {
                $q->where('Moderator', "1");
            })->select('ID_Person', 'First_Name', 'Last_Name')->get();

            return $this->returnData('people', $user);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function view_testers(Request $request)
    {
        try {
            $user = User::wherehas('permission', function ($q) {
                $q->where('Tester', "1");
            })->select('ID_Person', 'First_Name', 'Last_Name')->get();

            return $this->returnData('people', $user);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function view_assistants(Request $request)
    {
        try {
            $user = User::wherehas('permission', function ($q) {
                $q->where('Assisstent', "1");
            })->select('ID_Person', 'First_Name', 'Last_Name')->get();

            return $this->returnData('people', $user);

        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function get_data(Request $request)
    {
        try {
            $data = $request->only(
                'Data'


            );
            $user = User::all();
            foreach ($user as $i) {
                $i->Education_ID_Education = null;
                $i->save();
            }
            foreach ($request->Data as $i) {
                set_time_limit(120);
                $i = (object) $i;

                $first_name = null;
                if (property_exists($i, 'first_name')) {
                    $first_name = $i->first_name;
                }
                $last_name = null;
                if (property_exists($i, 'last_name')) {
                    $last_name = $i->last_name;
                }
                $father_name = null;
                if (property_exists($i, 'father_name')) {
                    $father_name = $i->father_name;
                    $father_phone = null;
                    if (property_exists($i, 'father_phone')) {

                        $father_phone = $i->father_phone;
                    }
                    if (property_exists($i, 'father_job')) {
                        $father_job = $i->father_job;
                    }
                }
                $birth_date = null;
                if (property_exists($i, 'birth_date')) {
                    $birth_date = $i->birth_date;
                }
                $call_phone = null;
                if (property_exists($i, 'call_phone')) {
                    $call_phone = $i->call_phone;
                }
                $social_phone = null;
                if (property_exists($i, 'social_phone')) {
                    $social_phone = $i->social_phone;
                }
                $address = null;
                if (property_exists($i, 'address')) {
                    $address = $i->address;
                }
                $note = null;
                if (property_exists($i, 'note')) {
                    $note = $i->note;
                }

                $check = User::where('First_Name', $first_name)->where('Last_Name', $last_name)->where('Birth_Date', $birth_date)->wherehas('father', function ($q) use ($father_name) {
                    $q->where('Father_Name', $father_name);
                })->first();
                if ($check) {
                    $education_id = null;
                    if (property_exists($i, 'class')) {


                        $education = Education::where('Education_Type', $i->class)->where('Major_ID_Major', null)->first();

                        if (!$education) {
                            $education = Education::create(['Education_Type' => $i->class]);
                        }
                        $education_id = $education->ID_Education;

                    }
                    $check->Education_ID_Education = $education_id;
                    $check->save();
                    continue;
                }

            }


        } catch (\Throwable $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function get_groups_members_phones()
    {
        $groups = Group::all();
        $people = [];
        foreach ($groups as $i) {
            $activestudents = Student::where("Group", $i->ID_Group)->where('State', 2)->get();
            $group = new stdClass;
            $group->name = $i->Group_Name;
            $group->students = [];
            foreach ($activestudents as $j) {
                $studentperson = User::where("ID_Person", $j->ID_Student_Pep)->select("ID_Person", "First_Name", "Last_Name", "Phone_Number_Social")->first();
                $phone = Phone::where("Phone", $studentperson->Phone_Number_Social)->first();
                if ($phone != null) {
                    $studentperson->Nubmer = $phone->Number;
                } else {
                    $studentperson->Nubmer = null;
                }
                array_push($group->students, $studentperson);
            }
            array_push($people, $group);


        }
        // Create a new Excel object
        $objPHPExcel = new PHPExcel();

        // Set the active sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $k = 1;
        for ($i = 0; $i < count($people); $i++) {

            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($k, 1)->setValue($people[$i]->name);
            for ($j = 2; $j <= count($people[$i]->students); $j++) {
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($k, $j)->setValue($people[$i]->students[$j - 2]->First_Name . ' ' . $people[$i]->students[$j - 2]->Last_Name);
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($k + 1, $j)->setValue($people[$i]->students[$j - 2]->Nubmer);

            }
            $k++;
            $k++;
        }
        // Set the value of cell A1


        // Save the Excel file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('example.xlsx');
        return $this->returnData('people', $people);
    }

    public function move_memoriazations(Request $request){
        try {
        $data = $request->only('ID_Sender', 'ID_Reciever');
        $memorizations = Reciting::where("Reciter_Pep", $request->ID_Sender)->get();
        foreach($memorizations as $i){
            $reciting = Reciting::where("Reciter_Pep", "ID_Reciever")->where("Page", $i->Page)->find();
            if($reciting == null){
                $i->Reciter_Pep = $request->ID_Reciever;
                $i->save();
            }
        }

    } catch (\Throwable $ex) {
        return $this->returnError($ex->getCode(), $ex->getMessage());
    }
    }

    public function move_attendance(Request $request){
        try {
        $data = $request->only('ID_Sender', 'ID_Reciever');
        $attendaces = Attendance_Evaluation::where("Students_Person_ID_Person", $request->ID_Sender)->get();
        foreach($attendaces as $i){
        $attendace = Attendance_Evaluation::where("Students_Person_ID_Person", $request->ID_Reciever)->where('created_at', $i->created_at)->get();
            if(count($attendace) == 0){
                $i->Students_Person_ID_Person = $request->ID_Reciever;
                $i->save();
           }
        }

    } catch (\Throwable $ex) {
        return $this->returnError($ex->getCode(), $ex->getMessage());
    }
    }

    public function move_memoriazations_with_check(Request $request){
        try {
        $data = $request->only('ID_Sender', 'ID_Reciever');
        $sender = User::find($request->ID_Sender);
        $reciever = User::find($request->ID_Reciever);
        if($sender->First_Name == $reciever->First_Name && $sender->Last_Name == $reciever->Last_Name){

            $sender_father = Father::find($sender->Fathers_ID_Father);
            $reciever_father = Father::find($reciever->Fathers_ID_Father);
            if($sender_father->Father_Name == $reciever_father->Father_Name){
                $memorizations = Reciting::where("Reciter_Pep", $request->ID_Sender)->get();
                foreach($memorizations as $i){
                    $reciting = Reciting::where("Reciter_Pep", "ID_Reciever")->where("Page", $i->Page)->find();
                    if($reciting == null){
                        $i->Reciter_Pep = $request->ID_Reciever;
                        $i->save();
                    }
                }
            }    
        }

      

    } catch (\Throwable $ex) {
        return $this->returnError($ex->getCode(), $ex->getMessage());
    }
    }

    public function get_orphans(){
        $orphans = User::wherehas('student',function($q){$q->where('State', 2);})->wherehas('father', function($q){$q->where('State_ID_State', 4)->orwhere('State_ID_State', 5);})->orwherehas('mother', function($q){$q->where('State_ID_State', 4)->orwhere('State_ID_State', 5);})->select('ID_Person', 'First_Name', 'Last_Name')->get();
        return $this->returnData('orphans', $orphans);
        
    }

    public function calculate_points(Request $request){
        echo 1;
                $attendance = Attendance_Evaluation::where('Students_Person_ID_Person', $request->ID_Person)->get();
        $points = 0;
        foreach($attendance as $i){
            if($i->State_Attendance == 6){
                $points += 5;
            }
            if($i->State_Garrment == 6){
                $points += 5;
            }
            if($i->State_Garrment == 6){
                $points +=5;
            }
        }

        $recitings = Reciting::wherenotin('Listner_Per', [1])->where('Reciter_Pep', $request->ID_Person)->get();
        echo 2;
        foreach($recitings as $i){
            $points += (((int)($i->Rates_ID_Rate))*5);

        }
        echo 3;
        $additional_points = Additional_Poit::where('Receiver_Pep', $request->ID_Person)->get();

        foreach($additional_points as $i){
            $points += $i->Points;
        }
        echo 4;
        $tests = Test::where('Tested_Pep', $request->ID_Person)->get();
        foreach($tests as $test){
            $points += $i->Mark;
            if($i->Tajweed != null){
                $points += $i->Tajweed;
            }

        }
        echo $points;
    }
}