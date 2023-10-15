<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kin extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Kin';

    protected $table = 'kins';
    protected $fillable = [
        'Kin_Name',    
        'State_ID_State',
        'Job_ID_Job',
        'Phones_Phone'
  
    ];

     ######################## Begin relations ##################

     public function state(){
        return $this -> belongsTo('App\Models\State', 'State_ID_State');
    }

    public function job(){
        return $this -> belongsTo('App\Models\Job', 'Job_ID_Job');
    }

    public function user(){
        return $this -> hasMany('App\Models\User', 'Kins_ID_Kin', 'ID_Kin');
    }

    public function phone(){
        return $this -> belongsTo('App\Models\Phone', 'Phones_Phone');
    }

    ######################## end relations ##################
}
