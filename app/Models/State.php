<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_State';
    protected $fillable = [
        'State_Name'
    ];

    ######################## Begin relations ##################



    public function user(){
        return $this -> hasMany('App\Models\User', 'State_ID_State', 'ID_State');
    }

    public function student(){
        return $this -> hasMany('App\Models\Student', 'State', 'ID_State');
    }

    public function father(){
        return $this -> hasMany('App\Models\Father', 'State_ID_State', 'ID_State');
    }

    public function mother(){
        return $this -> hasMany('App\Models\Mother', 'State_ID_State', 'ID_State');
    }

    public function kin(){
        return $this -> hasMany('App\Models\Kin', 'State_ID_State', 'ID_State');
    }

    public function permission(){
        return $this -> hasMany('App\Models\Permission', 'State', 'ID_State');
    }

    public function recitings(){
        return $this -> hasMany('App\Models\Recitings', 'State', 'ID_State');
    }

    public function checked_recitings(){
        return $this -> hasMany('App\Models\Recitings', 'Checked', 'ID_State');
    }

    public function attendance(){
        return $this -> hasMany('App\Models\Attendance_Evalutaion', 'State_Attendance', 'ID_State');
    }

    public function garrment(){
        return $this -> hasMany('App\Models\Attendance_Evalutaion', 'State_Garrment', 'ID_State');
    }

    public function behavior(){
        return $this -> hasMany('App\Models\Attendance_Evalutaion', 'State_Behavior', 'ID_State');
    }

    public function additional_points(){
        return $this -> hasMany('App\Models\Additional_Poit', 'Checked', 'ID_State');
    }

    public function tests(){
        return $this -> hasMany('App\Models\Test', 'State', 'ID_State');
    }

    public function checked_tests(){
        return $this -> hasMany('App\Models\Test', 'Checked', 'ID_State');
    }

    public function event(){
        return $this -> hasMany('App\Models\Event', 'State', 'ID_State');
    }

    ######################## end relations ##################
}
