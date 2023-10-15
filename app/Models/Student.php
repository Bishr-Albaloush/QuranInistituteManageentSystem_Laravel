<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $table = 'students';

    protected $primaryKey = 'ID_Student_Pep';
    protected $fillable = [   
            'ID_Student_Pep',
            'Register_Date',
            'Group',
            'State',
            'Memorizations',
    ];

      ######################## Begin relations ##################

    public function user(){
        return $this -> belongsTo('App\Models\User', 'ID_Student_Pep');
    }

    public function state(){
        return $this -> belongsTo('App\Models\State', 'State');
    }

    public function group(){
        return $this -> belongsTo('App\Models\Group', 'Group');
    }

    public function recitings(){
        return $this -> hasMany('App\Models\Reciting', 'Reciter_Pep', 'ID_Student_Pep');
    }

    public function attendance_evalutaion(){
        return $this -> hasMany('App\Models\Attendance_Evalutaion', 'Students_Person_ID_Person', 'ID_Student_Pep');
    }


    ######################## end relations ##################

}
