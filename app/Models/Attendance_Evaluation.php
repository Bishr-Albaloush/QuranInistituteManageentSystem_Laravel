<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Attendance_Evaluation extends Model
{
    use HasFactory;
    protected $table = 'attendance_evalutaion';

    protected $primaryKey = "ID_Attendance";

    protected $fillable = [
        'ID_Attendance',
        'Students_Person_ID_Person',
        'State_Attendance',
        'State_Garrment',
        'State_Behavior',
        'Group',
        'created_at'
    ];

    ######################## Begin relations ##################

    public function attendance()
    {
        return $this->belongsTo('App\Models\State', 'State_Attendance');
    }

    public function garrment()
    {
        return $this->belongsTo('App\Models\State', 'State_Garrment');
    }

    public function behavior()
    {
        return $this->belongsTo('App\Models\State', 'State_Behavior');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'Students_Person_ID_Person');
    }

    public function setMyDateFormat($value)
    {
        $this->attributes['created_at'] = date('d-m-Y', strtotime($value));
    }
   
   

    ######################## end relations ##################

}